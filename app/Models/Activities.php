<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;
    protected $table = "activities";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_client',
        'name',
        'status',
        'action',
        'note',
    ];

    public function clients(){
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }
}
