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
        'id_sales',
        'no_quote',
        'attn',
        'no_pr',
        'date',
        'title',
        'type',
        'week',
        'subtotal',
        'diskon',
        'tax',
        'tax_amount',
        'total',
        'note',
        'validity',
        'pricing',
        'delivery_process',
        'payment',
        'status',
        'po_number',
        'po_file',
        'payment_method',
    ];

    protected $casts = [
        'date' => 'date',
        'tax'  => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function pic()
    {
        return $this->belongsTo(Pic::class, 'id_pic');
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

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'id_unit_quotation');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'id_unit_quotation');
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
