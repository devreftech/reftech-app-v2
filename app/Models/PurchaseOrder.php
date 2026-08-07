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
        'category',
        'receipt_status',
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
        'total',
    ];
    public function detail()
    {
        return $this->hasMany('App\Models\DetailPurchaseOrder', 'id_purhcase_order');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }
}
