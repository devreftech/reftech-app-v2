<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailChangeWarehouse extends Model
{
    use HasFactory;
    protected $table = "detail_change_warehouse";
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_change_warehouse',
        'id_replacement',
        'qty',
    ];
    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'id_sender', 'id');
    }
    public function reciever()
    {
        return $this->belongsTo('App\Models\User', 'id_reciever', 'id');
    }
    public function replacement()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_replacement', 'id');
    }
}
