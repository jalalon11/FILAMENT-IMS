<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            static::logActivity('created', $model);
        });

        static::updated(function (Model $model) {
            static::logActivity('updated', $model);
        });

        static::deleted(function (Model $model) {
            static::logActivity('deleted', $model);
        });
    }

    protected static function logActivity(string $action, Model $model): void
    {
        $modelName = class_basename($model);
        $identifierField = static::getIdentifierField();
        $identifier = $model->{$identifierField} ?? $model->id;

        $description = match ($action) {
            'created' => "{$modelName} '{$identifier}' was created",
            'updated' => "{$modelName} '{$identifier}' was updated",
            'deleted' => "{$modelName} '{$identifier}' was deleted",
            default => "{$action} on {$modelName} '{$identifier}'",
        };

        $properties = [];

        if ($action === 'updated') {
            $properties['changes'] = $model->getChanges();
            $properties['original'] = array_intersect_key($model->getOriginal(), $model->getChanges());
        }

        if ($action === 'created') {
            $properties['attributes'] = $model->getAttributes();
        }

        ActivityLog::log($action, $description, $model, $properties);
    }

    protected static function getIdentifierField(): string
    {
        return 'name';
    }
}
