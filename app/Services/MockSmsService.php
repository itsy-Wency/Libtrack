<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MockSmsService
{
    /** Log the SMS payload in place of contacting a real SMS provider. */
    public function send(string $to, string $message): void
    {
        Log::info('SIMULATED SMS', ['to' => $to, 'message' => $message]);
    }
}
