<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    use HasFactory;
    protected $table = "purchase_request_detail";
    protected $date = [
        'purchase_date',
        'gr_date',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'id_purchase_request',
        'id_equivalent',
        'qty',
        'note',
        'price',
        'amount',
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
    public function header()
    {
        return $this->belongsTo('App\Models\PurchaseRequest', 'id_purchase_request', 'id');
    }
    public function equivalent()
    {
        return $this->belongsTo('App\Models\SerialProduct', 'id_equivalent', 'id');
    }
    public function allocations()
    {
        return $this->hasMany('App\Models\PurchaseRequestDetailAllocation', 'id_purchase_request_detail');
    }
    public function getAllocatedQtyAttribute()
    {
        return $this->allocations->sum('qty');
    }
    public function getRemainingQtyAttribute()
    {
        return max(0, $this->qty - $this->allocatedQty);
    }
}
