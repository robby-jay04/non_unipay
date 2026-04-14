<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false; // only created_at, managed by DB default

    protected $fillable = [
        'admin_user_id',
        'action_type',
        'module',
        'entity_type',
        'entity_id',
        'old_value',
        'new_value',
        'description',
        'severity',
        'ip_address',
        'user_agent',
        'session_id',
        'url',
        'http_method',
        'created_at',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
    ];

    // Prevent any updates or deletes at the model level
    public static function boot(): void
    {
        parent::boot();

        static::updating(fn() => throw new \LogicException('Audit logs are immutable.'));
        static::deleting(fn() => throw new \LogicException('Audit logs cannot be deleted.'));
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    // Scopes
    public function scopeOfSeverity($q, string $severity)
    {
        return $q->where('severity', $severity);
    }

    public function scopeForModule($q, string $module)
    {
        return $q->where('module', $module);
    }

    public function scopeForEntity($q, string $type, int $id)
    {
        return $q->where('entity_type', $type)->where('entity_id', $id);
    }

    public function scopeDateRange($q, ?string $from, ?string $to)
    {
        if ($from) $q->where('created_at', '>=', $from);
        if ($to)   $q->where('created_at', '<=', $to);
        return $q;
    }
}