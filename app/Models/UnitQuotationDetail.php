<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitQuotationDetail extends Model
{
    protected $table = 'unit_quotation_detail';

    protected $fillable = [
        'id_unit_quotation',
        'type',
        'id_unit',
        'id_fixed_asset',
        'id_equivalent',
        'spec_visible',
        'label',
        'description',
        'qty',
        'info_qty',
        'price',
        'disc',
        'amount',
        'pph',
        'sort_order',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class, 'id_fixed_asset');
    }

    public function equivalent()
    {
        return $this->belongsTo(SerialProduct::class, 'id_equivalent');
    }

    public function deliveredItems()
    {
        return $this->hasMany(DetailDelivery::class, 'id_unit_quotation_detail');
    }

    public function getDeliveredQtyAttribute()
    {
        return (float) $this->deliveredItems()->sum('qty');
    }

    public function getRemainingQtyAttribute()
    {
        return max((float) $this->qty - $this->delivered_qty, 0);
    }

    public function getSpecVisibleArray(): array
    {
        if (!$this->spec_visible) return [];
        return json_decode($this->spec_visible, true) ?? [];
    }
}
