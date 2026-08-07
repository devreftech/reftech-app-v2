<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailUser extends Model
{
    use HasFactory;
    protected $table = "detail_users";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_users',
        'position',
        'roles',
        'area',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_users', 'id');
    }
}
