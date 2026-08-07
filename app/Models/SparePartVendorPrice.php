<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartVendorPrice extends Model
{
    protected $table = 'spare_part_vendor_prices';

    protected $fillable = [
        'id_serial_product',
        'id_supplier',
        'price_usd',
        'kurs_usd',
        'price_idr',
        'date',
    ];

    protected $casts = [
        'date'      => 'date',
        'price_usd' => 'decimal:2',
        'kurs_usd'  => 'decimal:2',
        'price_idr' => 'decimal:2',
    ];

    public function serialProduct()
    {
        return $this->belongsTo(SerialProduct::class, 'id_serial_product');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
}
