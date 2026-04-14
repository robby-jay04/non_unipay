<?php

namespace App\Traits;

use App\Services\AuditLogger;

trait Auditable
{
    protected static array $auditData = [];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->writeAudit('created', null, $model->toArray());
        });

        static::updating(function ($model) {
            $old = collect($model->getOriginal())
                ->only(array_keys($model->getDirty()))
                ->toArray();
            $new = $model->getDirty();

            // Store in static array, not as model attributes
            static::$auditData[$model->getKey()] = ['old' => $old, 'new' => $new];
        });

        static::updated(function ($model) {
            $key = $model->getKey();
            if (isset(static::$auditData[$key])) {
                $data = static::$auditData[$key];
                $model->writeAudit('updated', $data['old'], $data['new']);
                unset(static::$auditData[$key]);
            }
        });

        static::deleting(function ($model) {
            $model->writeAudit('deleted', $model->toArray(), null);
        });
    }

    private function writeAudit(string $event, ?array $old, ?array $new): void
    {
        $module    = $this->auditModule ?? class_basename(static::class);
        $shortName = strtolower(class_basename(static::class));
        $action    = "{$shortName}.{$event}";

        app(AuditLogger::class)->log(
            actionType:  $action,
            module:      $module,
            description: ucfirst($event) . " {$module} #{$this->getKey()}",
            oldValue:    $old,
            newValue:    $new,
            entity:      $this,
        );
    }
}