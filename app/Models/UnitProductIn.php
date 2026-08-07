<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitProductIn extends Model
{
    protected $table = 'unit_product_in';

    protected $fillable = [
        'no_transaksi',
        'transaction_type',
        'id_po',
        'id_supplier',
        'id_customer',
        'date',
        'note',
        'created_by',
    ];

    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }

    public function detail()
    {
        return $this->hasMany(DetailUnitProductIn::class, 'id_unit_product_in', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
