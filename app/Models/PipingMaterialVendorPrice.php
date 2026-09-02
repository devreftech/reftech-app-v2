<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipingMaterialVendorPrice extends Model
{
    use HasFactory;

    protected $table = 'piping_material_vendor_prices';

    protected $fillable = [
        'id_piping_material',
        'id_supplier',
        'price_idr',
        'price_usd',
        'kurs_usd',
        'date',
        'notes',
        'is_primary',
    ];

    protected $casts = [
        'price_idr'  => 'decimal:2',
        'price_usd'  => 'decimal:2',
        'kurs_usd'   => 'decimal:2',
        'date'       => 'date',
        'is_primary' => 'boolean',
    ];

    public function material()
    {
        return $this->belongsTo(PipingMaterial::class, 'id_piping_material');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
}
