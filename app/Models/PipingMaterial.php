<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipingMaterial extends Model
{
    use HasFactory;

    protected $table = 'piping_materials';

    protected $fillable = [
        'item_code',
        'category',
        'material_type',
        'item_name',
        'size',
        'connection_type',
        'unit',
        'length_per_unit',
        'default_waste_percent',
        'notes',
    ];

    protected $casts = [
        'length_per_unit'       => 'decimal:2',
        'default_waste_percent' => 'decimal:2',
    ];

    public function vendorPrices()
    {
        return $this->hasMany(PipingMaterialVendorPrice::class, 'id_piping_material')->orderBy('price_idr', 'asc');
    }

    public function primaryVendorPrice()
    {
        return $this->hasOne(PipingMaterialVendorPrice::class, 'id_piping_material')->where('is_primary', true);
    }

    public function cheapestVendorPrice()
    {
        return $this->hasOne(PipingMaterialVendorPrice::class, 'id_piping_material')->orderBy('price_idr', 'asc');
    }

    public function getCheapestPriceIdrAttribute()
    {
        $cheapest = $this->vendorPrices->sortBy('price_idr')->first();
        return $cheapest ? $cheapest->price_idr : 0;
    }

    public function getCheapestSupplierNameAttribute()
    {
        $cheapest = $this->vendorPrices->sortBy('price_idr')->first();
        return $cheapest && $cheapest->supplier ? $cheapest->supplier->supplier : '-';
    }

    public function getFormattedCategoryAttribute()
    {
        return match ($this->category) {
            'pipe'       => 'Pipa (Pipe)',
            'fitting'    => 'Fitting / Sambungan',
            'valve'      => 'Valve & Instrument',
            'support'    => 'Support & Fastener',
            'consumable' => 'Bahan Habis Pakai',
            default      => ucfirst($this->category),
        };
    }
}
