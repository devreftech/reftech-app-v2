<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compressor extends Model
{
    use HasFactory;
    protected $table = "compressor";
    protected $fillable = [
        'compressor_brand',
        'series'
    ];

    
    public function detail_compressor()
    {
        return $this->hasMany('App\Models\DetailCompressor', 'id');
    }
    
}
