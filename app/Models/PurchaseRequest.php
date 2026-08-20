<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;
    protected $table = "purchase_request";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'no_pr',
        'no_gr',
        'id_pending',
        'id_user',
        'status',
        'gr_sent_at',
        'rejected_at',
        'rejected_reason',
        'rejected_by',
        'date',
    ];
    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
    public function details()
    {
        return $this->hasMany('App\Models\PurchaseRequestDetail', 'id_purchase_request');
    }
    public function purchaseOrders()
    {
        return $this->hasMany('App\Models\PurchaseOrder', 'id_purchase_request');
    }
    public function rejector()
    {
        return $this->belongsTo('App\Models\User', 'rejected_by', 'id');
    }
}
