<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            static::recordActivity($model, 'created', 'Membuat ' . class_basename($model) . ' baru.');
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }
            $original = array_intersect_key($model->getOriginal(), $dirty);
            
            static::recordActivity(
                $model,
                'updated',
                'Mengubah ' . class_basename($model) . ' #' . $model->getKey() . '.',
                [
                    'old_values' => $original,
                    'new_values' => $dirty,
                ]
            );
        });

        static::deleted(function ($model) {
            static::recordActivity($model, 'deleted', 'Menghapus ' . class_basename($model) . ' #' . $model->getKey() . '.');
        });
    }

    protected static function recordActivity($model, string $action, string $description, array $properties = [])
    {
        try {
            ActivityLog::create([
                'user_id'      => Auth::id(),
                'type'         => 'activity',
                'action'       => $action,
                'subject_type' => get_class($model),
                'subject_id'   => $model->getKey(),
                'description'  => $description,
                'properties'   => !empty($properties) ? $properties : null,
                'ip_address'   => Request::ip(),
                'user_agent'   => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Fail silently to avoid breaking main transaction
            \Log::error('Failed recording activity log: ' . $e->getMessage());
        }
    }
}
