<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;
    protected $table = "reminder";
    protected $date = [
        'date',
        'date_fu',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'reminder',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
