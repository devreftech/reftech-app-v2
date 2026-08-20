<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $table = "purchase_order";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'no_po',
        'no_gr',
        'no_invoice_supplier',
        'invoice_file',
        'category',
        'receipt_status',
        'gr_sent_at',
        'id_purchase_request',
        'attn',
        'mobile',
        'company',
        'email',
        'address',
        'phone',
        'payment',
        'delivery',
        'note',
        'subtotal',
        'vat',
        'delivery_cost',
        'total',
    ];
    public function detail()
    {
        return $this->hasMany('App\Models\DetailPurchaseOrder', 'id_purchase_order');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }
    public function purchaseRequest()
    {
        return $this->belongsTo('App\Models\PurchaseRequest', 'id_purchase_request', 'id');
    }
    public function prAllocations()
    {
        return $this->hasMany('App\Models\PurchaseRequestDetailAllocation', 'id_purchase_order');
    }
}
