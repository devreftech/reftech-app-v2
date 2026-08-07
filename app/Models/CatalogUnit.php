<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogUnit extends Model
{
    protected $table = 'catalog_unit';

    protected $fillable = [
        'id_unit', 'price_idr', 'price_usd', 'spec_note', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_usd' => 'float',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function priceHistory()
    {
        return $this->hasMany(CatalogUnitPriceHistory::class, 'id_catalog_unit')->orderByDesc('created_at');
    }
}
