<?php

namespace App\Console\Commands;

use App\Services\SystemMonitoringService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('system:health-check')]
#[Description('Probe the web application, database, and storage capacity.')]
class CaptureSystemHealth extends Command
{
    /** Run one availability sample and return a failure if it cannot be stored. */
    public function handle(SystemMonitoringService $monitoring): int
    {
        if (! $monitoring->captureHealthCheck()) {
            $this->error('The health sample could not be written to local storage.');

            return self::FAILURE;
        }

        $this->info('System health sample recorded.');

        return self::SUCCESS;
    }
}
