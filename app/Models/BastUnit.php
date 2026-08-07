<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BastUnit extends Model
{
    use HasFactory;

    protected $table = 'bast_units';

    protected $fillable = [
        'id_bast',
        'unit_name',
        'serial_no',
        'qty',
        'position',
    ];

    public function bast()
    {
        return $this->belongsTo(Bast::class, 'id_bast');
    }
}
