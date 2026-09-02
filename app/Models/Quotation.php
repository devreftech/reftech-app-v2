<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "quotation";
    protected $date = [
        'status_date',
        'expired_date',
        'estimated_date',
        'po_date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_pic',
        'id_sales',
        'id_admin',
        'id_service',
        'id_monitoring',
        'primary_id',
        'is_primary',
        'num_rev',
        'destination',
        'no_pr',
        'status',
        'note',
        'flag',
        'tax',
        'diskon',
        'fee',
        'nett',
        'po_file',
        'level',
        'shipping',
        'no_quote',
        'termcon',
        'comment',
        'commentAdmin',
        'subtotal',
        'total_no_tax',
        'harga_total'
    ];

    /**
     * Batasi ke quotation "primary" secara efektif: baris yang ditandai is_primary = 1,
     * ATAU kepala rantai revisi (primary_id = id) yang rantainya tidak punya baris
     * ber-is_primary = 1 sama sekali (data lama yang flag-nya tidak pernah ter-set).
     * Dipakai untuk rekap PO supaya quotation tunggal yang is_primary-nya '0' tetap terhitung.
     */
    public function scopeEffectivePrimary($query)
    {
        return $query->where(function ($q) {
            $q->where('quotation.is_primary', '1')
                ->orWhere(function ($q2) {
                    $q2->whereColumn('quotation.primary_id', 'quotation.id')
                        ->whereNotExists(function ($sub) {
                            $sub->selectRaw('1')
                                ->from('quotation as qp')
                                ->whereColumn('qp.primary_id', 'quotation.primary_id')
                                ->where('qp.is_primary', '1');
                        });
                });
        });
    }

    public function pic()
    {
        return $this->belongsTo('App\Models\Pic', 'id_pic', 'id');
    }
    public function sales()
    {
        return $this->belongsTo('App\Models\User', 'id_sales', 'id');
    }
    public function service()
    {
        return $this->belongsTo('App\Models\Service', 'id_service', 'id');
    }
    
    
    public function detail_quotation()
    {
        return $this->hasMany('App\Models\DetailQuotation', 'id_quotation');
    }
    public function termncon()
    {
        return $this->hasMany('App\Models\Termncon', 'id_quotation');
    }
    public function revisi()
    {
        return $this->hasMany('App\Models\RevQuote', 'id_quotation');
    }
    public function contract()
    {
        return $this->hasMany('App\Models\Contract', 'id_quotation');
    }
    public function invoice()
    {
        return $this->hasMany('App\Models\Invoice', 'id_quotation');
    }
    public function payment()
    {
        return $this->hasMany('App\Models\Payment', 'id_quotation');
    }
    public function status()
    {
        return $this->hasMany('App\Models\ChangeStatus', 'id_quotation');
    }
    public function return()
    {
        return $this->hasMany('App\Models\Retur', 'id_quotation');
    }
    public function suo()
    {
        return $this->hasOne('App\Models\Suo', 'id_quotation');
    }
}
