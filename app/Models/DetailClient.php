<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailClient extends Model
{
    use HasFactory;
    protected $table = "detail_client";
    protected $fillable = [
        'id_client',
        'id_detail_compressor'
    ];

    
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }
    public function detail_compressor()
    {
        return $this->belongsTo('App\Models\DetailCompressor', 'id_detail_compressor', 'id');
    }
    
}
