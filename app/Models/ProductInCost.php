<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInCost extends Model
{
    protected $table = 'product_in_costs';
    protected $fillable = [
        'id_product_in',
        'label',
        'amount',
        'created_by',
    ];

    public function productIn()
    {
        return $this->belongsTo(ProductIn::class, 'id_product_in', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
