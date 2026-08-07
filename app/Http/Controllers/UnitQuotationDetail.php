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

    public function getSpecVisibleArray(): array
    {
        if (!$this->spec_visible) return [];
        return json_decode($this->spec_visible, true) ?? [];
    }
}
