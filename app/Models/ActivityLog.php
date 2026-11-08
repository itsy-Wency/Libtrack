<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'details', 'ip_address', 'created_at'];

    /** Keep log timestamps available as date objects when rendered. */
    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    /** Return the user who performed the logged action, when available. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
