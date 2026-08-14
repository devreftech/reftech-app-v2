<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetailAllocation extends Model
{
    use HasFactory;
    protected $table = "purchase_request_detail_allocation";
    protected $fillable = [
        'id_purchase_request_detail',
        'id_purchase_order',
        'qty',
        'purchase_type',
        'cargo',
        'no_resi',
        'purchase_date',
    ];
    public function detail()
    {
        return $this->belongsTo('App\Models\PurchaseRequestDetail', 'id_purchase_request_detail', 'id');
    }
    public function purchaseOrder()
    {
        return $this->belongsTo('App\Models\PurchaseOrder', 'id_purchase_order', 'id');
    }
}
