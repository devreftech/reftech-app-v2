<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPmTemplateItem extends Model
{
    use HasFactory;

    protected $table = 'unit_pm_template_items';

    protected $fillable = [
        'id_unit',
        'level',
        'type',
        'id_equivalent',
        'label',
        'description',
        'qty',
        'info_qty',
        'price',
        'sort_order',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id');
    }

    public function equivalent()
    {
        return $this->belongsTo(SerialProduct::class, 'id_equivalent', 'id');
    }
}
