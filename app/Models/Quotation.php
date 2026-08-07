<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;
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
