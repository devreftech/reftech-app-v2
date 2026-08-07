<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = "supplier";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'supplier',
        'email',
        'phone',
        'address',
        'area',
        'npwp',
        'info',
    ];
    public function productIn()
    {
        return $this->hasMany('App\Models\ProductIn', 'id_supplier');
    }
    public function purchase()
    {
        return $this->hasMany('App\Models\PurchaseOrder', 'id_supplier');
    }
}
