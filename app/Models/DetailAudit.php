<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailAudit extends Model
{
    use HasFactory;
    protected $table = "detail_audit";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_audit',
        'tools',
        'qty',
        'desc',
        'assesment',
        'note',
    ];

    public function audit(){
        return $this->belongsTo('App\Models\User', 'id_audit', 'id');
    }
}
