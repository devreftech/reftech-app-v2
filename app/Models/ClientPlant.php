<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPlant extends Model
{
    use HasFactory;

    protected $table = "client_plants";
    protected $fillable = [
        'id_client',
        'name',
        'address',
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }
}
