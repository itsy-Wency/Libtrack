<?php

namespace App\Listeners;

use App\Services\SystemMonitoringService;
use Illuminate\Foundation\Http\Events\RequestHandled;

class RecordSystemRequestMetrics
{
    /** Resolve the best-effort counter service through Laravel's container. */
    public function __construct(private readonly SystemMonitoringService $monitoring) {}

    /** Count completed HTTP responses while excluding the probe request itself. */
    public function handle(RequestHandled $event): void
    {
        if ($event->request->is('up')) {
            return;
        }

        $this->monitoring->recordRequest($event->response->getStatusCode());
    }
}
