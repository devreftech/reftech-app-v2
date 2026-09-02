<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitQuotationOption extends Model
{
    protected $table = 'unit_quotation_options';

    protected $fillable = [
        'id_unit_quotation',
        'title',
        'sort_order',
        'subtotal',
        'diskon',
        'diskon_type',
        'tax',
        'tax_amount',
        'shipping',
        'total',
        'fee',
    ];

    protected $casts = [
        'tax' => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(UnitQuotation::class, 'id_unit_quotation');
    }

    public function details()
    {
        return $this->hasMany(UnitQuotationDetail::class, 'id_option')->orderBy('sort_order');
    }

    /** Nominal discount in Rupiah, regardless of whether it was entered as % or Rp */
    public function getDiscountAmountAttribute()
    {
        if (($this->diskon_type ?? 'percent') === 'amount') {
            return (float) $this->diskon;
        }
        return round($this->subtotal * $this->diskon / 100);
    }

    public function getDiscountLabelAttribute()
    {
        if (($this->diskon_type ?? 'percent') === 'amount') {
            return '';
        }
        return $this->diskon . '%';
    }
}
