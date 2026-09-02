<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipingRabSection extends Model
{
    use HasFactory;

    protected $table = 'piping_rab_sections';

    protected $fillable = [
        'id_piping_rab',
        'section_name',
        'sort_order',
        'subtotal_hpp',
        'subtotal_selling_price',
    ];

    protected $casts = [
        'subtotal_hpp'           => 'decimal:2',
        'subtotal_selling_price' => 'decimal:2',
    ];

    public function rab()
    {
        return $this->belongsTo(PipingRab::class, 'id_piping_rab');
    }

    public function items()
    {
        return $this->hasMany(PipingRabItem::class, 'id_piping_rab_section')->orderBy('sort_order', 'asc');
    }
}
