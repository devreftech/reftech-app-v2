<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCompressor extends Model
{
    use HasFactory;

    protected $table = "detail_compressor";
    protected $fillable = [
        'id_compressor',
        'compressor_type',
        'hp',
        'bar',
        'fad',
        'start_comissioning',
        'serial_number',
        'waranty',
    ];

    
    public function compressor()
    {
        return $this->belongsTo('App\Models\Compressor', 'id_compressor', 'id');
    }
    
}
