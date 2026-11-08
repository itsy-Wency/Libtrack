<?php
namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    /** Store an audit entry with the current user, request IP, and timestamp. */
    public function write(string $action, string $details = ''): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'details'    => $details,
            'ip_address' => request()->ip(),
            'created_at' => now()]);
    }
}
