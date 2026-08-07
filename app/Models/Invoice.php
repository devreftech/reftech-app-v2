<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = "invoice";
    protected $fillable = [
        'id_quotation',
        'id_unit_quotation',
        'term',
        'sign',
        'type',
        'percent',
        'flag',
        'no_po',
        'no_invoice',
        'date',
        'pph',
        'invoiceTo',
        'show_spec',
        'rejected_at',
        'rejected_reason',
        'rejected_by',
    ];

    /**
     * Invoice unit quotation yang masih menunggu diterbitkan (belum ada no_invoice
     * dan belum di-reject Accounting). Dipakai konsisten di semua tempat yang
     * menghitung/menampilkan "Request Invoice" supaya baris yang sudah di-reject
     * otomatis tidak muncul lagi di manapun.
     */
    public function scopePendingUnitRequest($query)
    {
        return $query->whereNotNull('id_unit_quotation')
            ->whereNull('no_invoice')
            ->whereNull('rejected_at');
    }

    public function quote()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }

    public function unitQuote()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo('App\Models\User', 'rejected_by', 'id');
    }
    public function expense()
    {
        return $this->hasMany('App\Model\Expense', 'id_invoice');
    }
    public function resi()
    {
        return $this->belongsTo('App\Models\Resi', 'id_invoice', 'id');
    }
}
