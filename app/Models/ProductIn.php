<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIn extends Model
{
    use HasFactory;
    protected $table = "product_in";
    protected $date = [
        'created_at',
        'updated_at',
        'date_invoice',
        'date'
    ];
    protected $fillable = [
        'no_product_in',
        'created_by',
        'no_do',
        'invoice',
        'supplier',
        'note',
        'subtotal',
        'total_no_tax',
        'shipping',
        'tax',
        'total',
        'price',
    ];
    public function supp()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailProductIn', 'id_product_in');
    }
    public function return()
    {
        return $this->hasMany('App\Models\Retur', 'id_product_in');
    }
}
