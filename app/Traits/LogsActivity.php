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
            $ref = static::resolveActivityReference($model);
            static::recordActivity(
                $model,
                'created',
                'Membuat ' . class_basename($model) . ' baru' . ($ref ? " (No: {$ref})" : '') . '.',
                $ref ? ['reference_no' => $ref] : []
            );
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }
            $original = array_intersect_key($model->getOriginal(), $dirty);
            $ref = static::resolveActivityReference($model);

            static::recordActivity(
                $model,
                'updated',
                'Mengubah ' . class_basename($model) . ' #' . $model->getKey() . ($ref ? " (No: {$ref})" : '') . '.',
                [
                    'reference_no' => $ref,
                    'old_values' => $original,
                    'new_values' => $dirty,
                ]
            );
        });

        static::deleted(function ($model) {
            $ref = static::resolveActivityReference($model);
            static::recordActivity(
                $model,
                'deleted',
                'Menghapus ' . class_basename($model) . ' #' . $model->getKey() . ($ref ? " (No: {$ref})" : '') . '.',
                $ref ? ['reference_no' => $ref] : []
            );
        });
    }

    /**
     * Common "reference number" column names across transactional models
     * (quotation, invoice, PO, BAST, contract, dsb) so the activity log
     * description can show the actual document number instead of just an ID.
     */
    protected static function resolveActivityReference($model)
    {
        if (method_exists($model, 'activityLogReferenceLabel')) {
            return $model->activityLogReferenceLabel();
        }

        $candidates = [
            'no_quote', 'rev_no_quote', 'no_invoice', 'no_invoice_supplier', 'no_invoice_booking',
            'no_po', 'no_gr', 'no_bast', 'no_contract', 'no_delivery', 'no_do',
            'no_product_in', 'no_product_out', 'no_pending', 'no_return', 'no_ticket',
            'no_voucher', 'no_cheque', 'no_expense', 'no_reg', 'SJ', 'BA', 'code', 'kode',
        ];

        foreach ($candidates as $field) {
            $value = $model->{$field} ?? null;
            if (!empty($value)) {
                return $value;
            }
        }

        return null;
    }

    protected static function recordActivity($model, string $action, string $description, array $properties = [])
    {
        try {
            $properties = array_filter($properties, fn ($v) => $v !== null);

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
