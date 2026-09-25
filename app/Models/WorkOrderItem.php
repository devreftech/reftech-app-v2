<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderItem extends Model
{
    use HasFactory;

    protected $table = 'work_order_items';

    protected $fillable = [
        'id_work_order',
        'item_name',
        'id_product',
        'id_detail_product',
        'id_equivalent',
        'warehouse',
        'qty_requested',
        'unit',
        'qty_approved',
        'qty_issued',
        'stock_at_check',
        'unit_price',
        'subtotal',
        'needs_pr',
        'note',
    ];

    protected $casts = [
        'qty_requested' => 'decimal:2',
        'qty_approved' => 'decimal:2',
        'qty_issued' => 'decimal:2',
        'stock_at_check' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'needs_pr' => 'boolean',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'id_work_order');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function detailProduct()
    {
        return $this->belongsTo(DetailProduct::class, 'id_detail_product');
    }

    public function equivalent()
    {
        return $this->belongsTo(SerialProduct::class, 'id_equivalent');
    }
}
