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
        'has_trade_in',
        'trade_in_brand',
        'trade_in_model',
        'trade_in_power',
        'trade_in_sn',
        'trade_in_price',
        'trade_in_notes',
        'note',
        'validity',
        'pricing',
        'payment',
        'warranty',
        'delivery_process',
        'rental_terms',
    ];

    protected $casts = [
        'tax'          => 'boolean',
        'has_trade_in' => 'boolean',
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

    /**
     * Term & Condition tiap opsi disimpan sendiri-sendiri (diisi lewat card T&C
     * di form create/edit yang ikut opsi aktif). Fallback ke unit_quotation (parent)
     * cuma buat opsi lama sebelum fitur ini ada, yang kolom-kolom ini masih null.
     */
    public function getEffectiveNoteAttribute()
    {
        return $this->note ?? $this->quotation->note;
    }

    public function getEffectiveValidityAttribute()
    {
        return $this->validity ?? $this->quotation->validity;
    }

    public function getEffectivePricingAttribute()
    {
        return $this->pricing ?? $this->quotation->pricing;
    }

    public function getEffectivePaymentAttribute()
    {
        return $this->payment ?? $this->quotation->payment;
    }

    public function getEffectiveWarrantyAttribute()
    {
        return $this->warranty ?? $this->quotation->warranty;
    }

    public function getEffectiveDeliveryProcessAttribute()
    {
        return $this->delivery_process ?? $this->quotation->delivery_process;
    }

    public function getEffectiveRentalTermsAttribute()
    {
        return $this->rental_terms ?? $this->quotation->rental_terms;
    }
}
