<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $table = 'unit';
    
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'sku',
        'brand',
        'model',
        'type_unit',
        'desc',
        'warehouse_stock',
        'frist_stock',
        'stock',
        'note',
        'sn',
        'unit',
        'bar',
        'power',
        'air_cap',
        'voltage',
        'connect',
        'cooling',
        'exhaust',
        'dimension',
        'weight',
        'refrigerant_type',
        'pdp',
        'filtration',
        'oil_content',
        'grade',
        'capacity',
        'material',
        'test_pressure',
        'inlet_pressure',
        'outlet_pressure',
        'inlet_cap',
        'outlet_cap',
        'status',
        'type',
        'generation',
    ];
    
    public function detail()
    {
        return $this->hasMany('App\Models\DetailProduct', 'id_product');
    }
    public function serial()
    {
        return $this->hasMany('App\Models\SerialProduct', 'id_product');
    }
    public function machine()
    {
        return $this->hasMany('App\Models\Machine', 'id_product');
    }
}
