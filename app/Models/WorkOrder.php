<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'work_orders';

    protected $fillable = [
        'no_wo',
        'id_fixed_asset',
        'id_machine',
        'id_user_created',
        'id_user_technician',
        'id_user_warehouse',
        'id_user_accounting',
        'date',
        'target_date',
        'status',
        'accounting_treatment',
        'id_purchase_request',
        'id_product_out',
        'description',
        'warehouse_note',
        'accounting_note',
        'rejected_reason',
        'total_cost',
    ];

    protected $casts = [
        'date' => 'date',
        'target_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class, 'id_fixed_asset');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'id_machine');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'id_user_created');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'id_user_technician');
    }

    public function warehouseUser()
    {
        return $this->belongsTo(User::class, 'id_user_warehouse');
    }

    public function accountingUser()
    {
        return $this->belongsTo(User::class, 'id_user_accounting');
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'id_purchase_request');
    }

    public function productOut()
    {
        return $this->belongsTo(ProductOut::class, 'id_product_out');
    }

    public function items()
    {
        return $this->hasMany(WorkOrderItem::class, 'id_work_order');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['class' => 'bg-label-secondary', 'text' => 'Draft', 'icon' => 'mdi-file-edit-outline'],
            'pending_warehouse' => ['class' => 'bg-label-warning', 'text' => 'Verifikasi Gudang', 'icon' => 'mdi-warehouse'],
            'waiting_pr' => ['class' => 'bg-label-info', 'text' => 'Menunggu PR (Pengadaan)', 'icon' => 'mdi-cart-clock'],
            'pending_accounting' => ['class' => 'bg-label-primary', 'text' => 'Persetujuan Accounting', 'icon' => 'mdi-shield-account-outline'],
            'approved' => ['class' => 'bg-label-success', 'text' => 'Disetujui (Siap Dikeluarkan)', 'icon' => 'mdi-check-decagram-outline'],
            'issued' => ['class' => 'bg-label-info', 'text' => 'Barang Dikeluarkan (Selesai)', 'icon' => 'mdi-check-all'],
            'rejected' => ['class' => 'bg-label-danger', 'text' => 'Ditolak', 'icon' => 'mdi-close-circle-outline'],
            'cancelled' => ['class' => 'bg-label-dark', 'text' => 'Dibatalkan', 'icon' => 'mdi-cancel'],
            default => ['class' => 'bg-label-secondary', 'text' => ucfirst(str_replace('_', ' ', $this->status)), 'icon' => 'mdi-help-circle-outline'],
        };
    }
}
