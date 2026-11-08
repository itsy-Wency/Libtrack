<?php
namespace App\Services;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Throwable;

/** Collect best-effort request counters and sampled service health for the admin view. */
class SystemMonitoringService
{
    /** Minute samples in the rolling availability window. */
    private const MONITORING_WINDOW_MINUTES = 1440;

    /** Inject the database manager used by the scheduled database probe. */
    public function __construct(private readonly DatabaseManager $database)
    {}

    /** Increment short-lived request and server-error counters without affecting responses. */
    public function recordRequest(int $statusCode): void
    {
        $minuteKey = now()->startOfMinute()->format('YmdHi');

        try {
            $requestKey = "system-monitor:requests:{$minuteKey}";
            Cache::add($requestKey, 0, now()->addMinutes(10));
            Cache::increment($requestKey);

            if ($statusCode >= 500) {
                $errorKey = "system-monitor:errors:{$minuteKey}";
                Cache::add($errorKey, 0, now()->addMinutes(10));
                Cache::increment($errorKey);
            }
        } catch (Throwable) {
            // Monitoring is best effort and must not interrupt the response.
        }
    }

    /** Probe the web URL, database, and storage, then append a local health sample. */
    public function captureHealthCheck(): bool
    {
        $checkedAt = now();
        $this->pruneRequestCounters($checkedAt);
        $applicationAvailable = $this->applicationAvailable();
        $databaseAvailable    = false;

        try {
            $this->database->connection()->select('SELECT 1');
            $databaseAvailable = true;
        } catch (Throwable) {
            // A failed database probe is recorded in the local health history.
        }

        $storageRoot     = storage_path();
        $storageWritable = is_writable($storageRoot)
        && is_writable(is_dir(storage_path('app/private')) ? storage_path('app/private') : storage_path('app'));
        $totalBytes = $this->byteCount(@disk_total_space($storageRoot));
        $freeBytes  = $this->byteCount(@disk_free_space($storageRoot));
        $directory  = storage_path('app/private/system-monitor');
        $snapshot   = [
            'checked_at'            => $checkedAt->toIso8601String(),
            'healthy'               => $applicationAvailable && $databaseAvailable && $storageWritable,
            'application_available' => $applicationAvailable,
            'database_available'    => $databaseAvailable,
            'storage_writable'      => $storageWritable,
            'storage_total_bytes'   => $totalBytes,
            'storage_free_bytes'    => $freeBytes,
        ];

        try {
            File::ensureDirectoryExists($directory);
            File::append(
                $directory . '/health-' . $checkedAt->toDateString() . '.jsonl',
                json_encode($snapshot, JSON_THROW_ON_ERROR) . PHP_EOL,
            );

            if ($checkedAt->hour === 0 && $checkedAt->minute === 0) {
                $this->pruneOldHealthFiles($directory, $checkedAt);
            }

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /** Read current counters and recent health samples for the admin dashboard.
     *
     * @return array{
     *     uptime: string,
     *     downtime: string,
     *     uptime_percent: ?float,
     *     coverage_percent: float,
     *     checks_recorded: int,
     *     requests_per_minute: int,
     *     five_minute_requests: int,
     *     error_rate_percent: ?float,
     *     active_users: ?int,
     *     storage: array{used: string, total: string, free: string, usage_percent: ?float},
     *     health: array{status: string, healthy: ?bool, application: string, database: string, storage: string, checked_at: ?string}
     * }
     */
    public function dashboardMetrics(): array
    {
        $samples            = $this->healthSamplesSince(now()->subDay());
        $healthyChecks      = count(array_filter($samples, fn(array $sample): bool => $sample['healthy']));
        $failedChecks       = count($samples) - $healthyChecks;
        $recordedChecks     = count($samples);
        $latestSample       = $samples === [] ? null : $samples[array_key_last($samples)];
        $requestsPerMinute  = $this->requestCountForMinute(now()->startOfMinute());
        $fiveMinuteRequests = 0;
        $fiveMinuteErrors   = 0;

        for ($minutesAgo = 0; $minutesAgo < 5; $minutesAgo++) {
            $minute              = now()->startOfMinute()->subMinutes($minutesAgo);
            $minuteKey           = $minute->format('YmdHi');
            $fiveMinuteRequests += (int) Cache::get("system-monitor:requests:{$minuteKey}", 0);
            $fiveMinuteErrors   += (int) Cache::get("system-monitor:errors:{$minuteKey}", 0);
        }

        $storage         = $this->storageMetrics($latestSample);
        $latestCheckTime = $latestSample === null ? null : Carbon::parse($latestSample['checked_at']);
        $status          = match (true) {
            $latestSample === null                     => 'Waiting for first scheduled check',
            $latestCheckTime->lt(now()->subMinutes(2)) => 'No recent check',
            $latestSample['healthy']                   => 'All checks passing',
            default                                    => 'Dependencies degraded',
        };

        return [
            'uptime'               => $recordedChecks === 0 ? 'No data' : $this->formatDuration($healthyChecks * 60),
            'downtime'             => $recordedChecks === 0 ? 'No data' : $this->formatDuration($failedChecks * 60),
            'uptime_percent'       => $recordedChecks === 0 ? null : round(($healthyChecks / $recordedChecks) * 100, 1),
            'coverage_percent'     => round(min(($recordedChecks / self::MONITORING_WINDOW_MINUTES) * 100, 100), 1),
            'checks_recorded'      => $recordedChecks,
            'requests_per_minute'  => $requestsPerMinute,
            'five_minute_requests' => $fiveMinuteRequests,
            'error_rate_percent'   => $fiveMinuteRequests === 0 ? null : round(($fiveMinuteErrors / $fiveMinuteRequests) * 100, 1),
            'active_users'         => $this->activeUserCount(),
            'storage'              => $storage,
            'health'               => [
                'status'      => $status,
                'healthy'     => $latestSample['healthy'] ?? null,
                'application' => $latestSample === null ? 'No data' : ($latestSample['application_available'] ? 'Responding' : 'Unavailable'),
                'database'    => $latestSample === null ? 'No data' : ($latestSample['database_available'] ? 'Connected' : 'Unavailable'),
                'storage'     => $latestSample === null ? 'No data' : ($latestSample['storage_writable'] ? 'Writable' : 'Not writable'),
                'checked_at'  => $latestSample['checked_at'] ?? null,
            ],
        ];
    }

    /** Read the request counter for the selected minute bucket. */
    private function requestCountForMinute(Carbon $minute): int
    {
        return (int) Cache::get('system-monitor:requests:' . $minute->format('YmdHi'), 0);
    }

    /** Check the URL configured for the app's built-in health endpoint. */
    private function applicationAvailable(): bool
    {
        try {
            $response = Http::connectTimeout(2)
                ->timeout(3)
                ->get(rtrim((string) config('app.url'), '/') . '/up');

            return $response->successful();
        } catch (Throwable) {
            return false;
        }
    }

    /** Count distinct authenticated sessions active in the last five minutes. */
    private function activeUserCount(): ?int
    {
        if (config('session.driver') !== 'database') {
            return null;
        }

        return DB::table(config('session.table', 'sessions'))
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->distinct()
            ->count('user_id');
    }

    /** Convert filesystem byte totals into display values and a usage percentage.
     *
     * @param  array<string, mixed>|null  $latestSample
     * @return array{used: string, total: string, free: string, usage_percent: ?float}
     */
    private function storageMetrics(?array $latestSample): array
    {
        $totalBytes = $latestSample['storage_total_bytes'] ?? $this->byteCount(@disk_total_space(storage_path()));
        $freeBytes  = $latestSample['storage_free_bytes'] ?? $this->byteCount(@disk_free_space(storage_path()));
        $usedBytes  = $totalBytes === null || $freeBytes === null ? null : max($totalBytes - $freeBytes, 0);

        return [
            'used'          => $this->formatBytes($usedBytes),
            'total'         => $this->formatBytes($totalBytes),
            'free'          => $this->formatBytes($freeBytes),
            'usage_percent' => $totalBytes === null || $totalBytes === 0 || $usedBytes === null
                ? null
                : round(($usedBytes / $totalBytes) * 100, 1),
        ];
    }

    /** Read and deduplicate persisted health samples inside the rolling window.
     *
     * @return list<array{checked_at: string, healthy: bool, application_available: bool, database_available: bool, storage_writable: bool, storage_total_bytes: ?int, storage_free_bytes: ?int}>
     */
    private function healthSamplesSince(Carbon $start): array
    {
        $directory       = storage_path('app/private/system-monitor');
        $samplesByMinute = [];
        $dates           = [$start->toDateString(), now()->toDateString()];

        foreach (array_unique($dates) as $date) {
            $path = $directory . '/health-' . $date . '.jsonl';

            if (! File::exists($path)) {
                continue;
            }

            try {
                foreach (preg_split('/\R/', File::get($path), flags: PREG_SPLIT_NO_EMPTY) ?: [] as $line) {
                    $sample = json_decode($line, true);

                    if (! is_array($sample) || ! isset(
                        $sample['checked_at'],
                        $sample['healthy'],
                        $sample['application_available'],
                        $sample['database_available'],
                        $sample['storage_writable'],
                    )) {
                        continue;
                    }

                    $checkedAt = Carbon::parse($sample['checked_at']);

                    if ($checkedAt->lt($start) || $checkedAt->gt(now())) {
                        continue;
                    }

                    $samplesByMinute[$checkedAt->format('YmdHi')] = [
                        'checked_at'            => $checkedAt->toIso8601String(),
                        'healthy'               => (bool) $sample['healthy'],
                        'application_available' => (bool) ($sample['application_available'] ?? false),
                        'database_available'    => (bool) ($sample['database_available'] ?? false),
                        'storage_writable'      => (bool) ($sample['storage_writable'] ?? false),
                        'storage_total_bytes'   => isset($sample['storage_total_bytes']) ? (int) $sample['storage_total_bytes'] : null,
                        'storage_free_bytes'    => isset($sample['storage_free_bytes']) ? (int) $sample['storage_free_bytes'] : null,
                    ];
                }
            } catch (Throwable) {
                continue;
            }
        }

        $samples = array_values($samplesByMinute);
        usort($samples, fn(array $left, array $right): int => strcmp($left['checked_at'], $right['checked_at']));

        return $samples;
    }

    /** Delete request counters after their ten-minute cache window expires. */
    private function pruneRequestCounters(Carbon $checkedAt): void
    {
        $expiredMinuteKey = $checkedAt->copy()->startOfMinute()->subMinutes(11)->format('YmdHi');

        try {
            Cache::forget("system-monitor:requests:{$expiredMinuteKey}");
            Cache::forget("system-monitor:errors:{$expiredMinuteKey}");
        } catch (Throwable) {
            // Cache cleanup must not prevent a health sample from being recorded.
        }
    }

    /** Keep only recent daily health files to bound local monitoring storage. */
    private function pruneOldHealthFiles(string $directory, Carbon $checkedAt): void
    {
        foreach (File::files($directory) as $file) {
            if (preg_match('/^health-(\d{4}-\d{2}-\d{2})\.jsonl$/', $file->getFilename(), $matches) !== 1) {
                continue;
            }

            if (Carbon::parse($matches[1])->lt($checkedAt->copy()->subDays(2)->startOfDay())) {
                File::delete($file->getPathname());
            }
        }
    }

    /** Convert disk-space results to integers while preserving unavailable values. */
    private function byteCount(float | int | false $bytes): ?int
    {
        return $bytes === false ? null : (int) $bytes;
    }

    /** Format a byte count with a readable binary unit. */
    private function formatBytes(?int $bytes): string
    {
        if ($bytes === null) {
            return 'Unavailable';
        }

        $value = (float) $bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'TB') {
                return number_format($value, $unit === 'B' ? 0 : 1) . ' ' . $unit;
            }

            $value /= 1024;
        }

        return number_format($value, 1) . ' TB';
    }

    /** Format sampled seconds as hours and minutes for the dashboard. */
    private function formatDuration(int $seconds): string
    {
        $hours   = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($hours === 0) {
            return $minutes . 'm';
        }

        return $hours . 'h ' . $minutes . 'm';
    }
}
