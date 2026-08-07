<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingPO extends Model
{
    use HasFactory;
    protected $table = "pending_po";
    protected $fillable = [
        'id_quotation',
        'id_unit_quotation',
        'ekspidisi',
        'status',
        'type',
        'title',
        'no_pending',
        'delivery',
        'date',
        'project_category',
        'project_status_step',
        'doc_address_type',
        'doc_address_manual',
        'shipping_address_type',
        'shipping_address_manual',
        'combine_shipping_and_parts',
        'doc_recipient_id',
        'shipping_recipient_id',
    ];
    public function quote()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }

    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }
    public function doc_recipient()
    {
        return $this->belongsTo('App\Models\Pic', 'doc_recipient_id', 'id');
    }
    public function shipping_recipient()
    {
        return $this->belongsTo('App\Models\Pic', 'shipping_recipient_id', 'id');
    }
    public function product_out()
    {
        return $this->belongsTo('App\Models\ProductOut', 'id_product_out', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailPendingPO', 'id_pending');
    }
    public function service()
    {
        return $this->hasMany('App\Models\ServiceOrder', 'id_sales_order');
    }
    public function pr()
    {
        return $this->hasMany('App\Models\PurchaseRequest', 'id_pending');
    }
    public function return()
    {
        return $this->hasMany('App\Models\Retur', 'id_pending');
    }
    public function expanse()
    {
        return $this->belongsTo('App\Models\Expanse', 'id_pending', 'id');
    }
}
