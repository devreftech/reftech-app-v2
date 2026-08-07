<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;
    protected $table = "audit";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_technician',
        'no_audit',
        'note',
    ];

    public function technician(){
        return $this->belongsTo('App\Models\User', 'id_technician', 'id');
    }
    public function audit()
    {
        return $this->hasMany('App\Models\Audit', 'id_detail_audit');
    }
}
