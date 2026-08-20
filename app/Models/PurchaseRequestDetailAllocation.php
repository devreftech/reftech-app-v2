<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetailAllocation extends Model
{
    use HasFactory;
    protected $table = "purchase_request_detail_allocation";
    protected $date = [
        'purchase_date',
        'gr_date',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'id_purchase_request_detail',
        'id_purchase_order',
        'qty',
        'purchase_type',
        'cargo',
        'no_resi',
        'purchase_date',
        'qty_received',
        'gr_status',
        'gr_note',
        'no_do',
        'gr_date',
        'warehouse',
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
