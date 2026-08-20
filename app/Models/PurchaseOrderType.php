<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderType extends Model
{
    protected $table = 'purchase_order_types';
    protected $fillable = [
        'name',
    ];
}
