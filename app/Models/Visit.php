<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $table = "visit";

    protected $data = [
        'date',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id_client',
        'status',
        'compressor_date',
        'running_hour',
        'note',
        'recommendation',
    ];

    
    public function client()
    {
        return $this->belongsTo('App\Model\Client', 'id_client', 'id');
    }
    
}
