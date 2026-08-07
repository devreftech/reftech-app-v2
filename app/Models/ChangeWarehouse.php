<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeWarehouse extends Model
{
    use HasFactory;
    protected $table = "change_warehouse";
    protected $dates = [
        'date',
        'date_recieve',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_sender',
        'id_reciever',
        'title',
        'status',
        'note',
        'note_recieve',
        'from',
        'to',
        'date', 
        'date_recieve', 
    ];
    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'id_sender', 'id');
    }
    public function reciever()
    {
        return $this->belongsTo('App\Models\User', 'id_reciever', 'id');
    }
}
