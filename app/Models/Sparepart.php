<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{use HasFactory;
    protected $table = "sparepart";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'qty',
        'qty_info',
        'pm_level',
    ];
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id_unit', 'id');
    }
    public function equivalent()
    {
        return $this->belongsTo('App\Models\SerialProduct', 'id_equivalent', 'id');
    }
}
