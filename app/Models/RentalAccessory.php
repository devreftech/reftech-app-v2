<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalAccessory extends Model
{
    use HasFactory;

    protected $table = 'rental_accessories';

    protected $fillable = [
        'category',
        'code',
        'id_purchase_order',
        'name',
        'brand',
        'size',
        'length',
        'max_pressure',
        'connection',
        'material',
        'extra_spec',
        'stock',
        'condition',
        'rental_status',
        'location',
        'notes',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_purchase_order');
    }

    public static function categories()
    {
        return [
            'hose' => 'Flexible Hose',
            'header' => 'Header',
            'reducer' => 'Reducer',
            'cable' => 'Kabel Power',
            'nipple' => 'Double Nipple',
        ];
    }
}
