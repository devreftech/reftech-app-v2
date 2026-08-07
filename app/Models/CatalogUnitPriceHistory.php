<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogUnitPriceHistory extends Model
{
    protected $table = 'catalog_unit_price_history';
    public $timestamps = false;

    protected $fillable = [
        'id_catalog_unit', 'price_idr', 'price_usd', 'changed_by', 'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'price_usd'  => 'float',
    ];

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
