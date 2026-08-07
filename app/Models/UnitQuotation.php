<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitQuotation extends Model
{
    protected $table = 'unit_quotation';

    protected $fillable = [
        'root_id',
        'revision_number',
        'is_latest',
        'id_client',
        'id_pic',
        'id_plant',
        'address',
        'id_sales',
        'id_support',
        'no_quote',
        'attn',
        'no_pr',
        'date',
        'expired_date',
        'title',
        'type',
        'week',
        'subtotal',
        'diskon',
        'diskon_type',
        'tax',
        'tax_amount',
        'shipping',
        'total',
        'note',
        'validity',
        'pricing',
        'warranty',
        'delivery_process',
        'payment',
        'status',
        'cancel_request',
        'po_number',
        'po_file',
        'po_received',
        'payment_method',
    ];

    protected $casts = [
        'date'         => 'date',
        'expired_date' => 'date',
        'tax'          => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function pic()
    {
        return $this->belongsTo(Pic::class, 'id_pic');
    }

    public function plant()
    {
        return $this->belongsTo(ClientPlant::class, 'id_plant');
    }

    public function sales()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_sales');
    }

    public function details()
    {
        return $this->hasMany(UnitQuotationDetail::class, 'id_unit_quotation')->orderBy('sort_order');
    }

    public function statusHistory()
    {
        return $this->hasMany(UnitQuotationStatusHistory::class, 'id_unit_quotation')->orderBy('created_at', 'desc');
    }

    public function comments()
    {
        return $this->hasMany(UnitQuotationComment::class, 'id_unit_quotation')->orderBy('created_at', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'id_unit_quotation');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_unit_quotation');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'id_unit_quotation');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'id_unit_quotation');
    }

    public function suo()
    {
        return $this->hasOne(Suo::class, 'id_unit_quotation');
    }

    public function getHargaTotalAttribute()
    {
        return $this->total;
    }

    /** Nominal discount in Rupiah, regardless of whether it was entered as % or Rp */
    public function getDiscountAmountAttribute()
    {
        if (($this->diskon_type ?? 'percent') === 'amount') {
            return (float) $this->diskon;
        }
        return round($this->subtotal * $this->diskon / 100);
    }

    /**
     * Discount rate descriptor, e.g. "10%" for a percent discount.
     * Empty for a flat Rupiah discount — the amount is already shown
     * next to it, so repeating it here would just be redundant.
     */
    public function getDiscountLabelAttribute()
    {
        if (($this->diskon_type ?? 'percent') === 'amount') {
            return '';
        }
        return $this->diskon . '%';
    }

    /** All revisions in the same group (including original) */
    public function allVersions()
    {
        $rootId = $this->root_id ?? $this->id;
        return UnitQuotation::where(function ($q) use ($rootId) {
            $q->where('id', $rootId)->orWhere('root_id', $rootId);
        })->orderBy('revision_number')->get(['id', 'no_quote', 'revision_number']);
    }
}
