<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipingRabItem extends Model
{
    use HasFactory;

    protected $table = 'piping_rab_items';

    protected $fillable = [
        'id_piping_rab_section',
        'id_piping_material',
        'item_type',
        'item_name',
        'size',
        'spec',
        'unit',
        'input_length_meter',
        'length_per_unit',
        'waste_percent',
        'calculated_qty',
        'unit_price_hpp',
        'id_supplier',
        'margin_type',
        'margin_value',
        'unit_selling_price',
        'total_hpp',
        'total_selling_price',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'input_length_meter'  => 'decimal:2',
        'length_per_unit'     => 'decimal:2',
        'waste_percent'       => 'decimal:2',
        'calculated_qty'      => 'decimal:2',
        'unit_price_hpp'      => 'decimal:2',
        'margin_value'        => 'decimal:2',
        'unit_selling_price'  => 'decimal:2',
        'total_hpp'           => 'decimal:2',
        'total_selling_price' => 'decimal:2',
    ];

    public function section()
    {
        return $this->belongsTo(PipingRabSection::class, 'id_piping_rab_section');
    }

    public function material()
    {
        return $this->belongsTo(PipingMaterial::class, 'id_piping_material');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
}
