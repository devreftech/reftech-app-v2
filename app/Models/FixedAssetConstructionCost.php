<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAssetConstructionCost extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'fixed_asset_construction_costs';

    protected $casts = [
        'tanggal' => 'date',
        'qty' => 'float',
        'harga_satuan' => 'float',
        'total_biaya' => 'float',
    ];

    protected $fillable = [
        'fixed_asset_id',
        'purchase_order_id',
        'tanggal',
        'kategori_biaya',
        'nama_item',
        'supplier_id',
        'payee',
        'qty',
        'satuan',
        'harga_satuan',
        'total_biaya',
        'no_bukti',
        'foto_bukti',
        'id_pengeluaran',
        'id_beban',
        'expense_id',
        'catatan',
        'created_by',
    ];

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class, 'fixed_asset_id', 'id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Account::class, 'id_pengeluaran', 'id');
    }

    public function beban()
    {
        return $this->belongsTo(Account::class, 'id_beban', 'id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
