<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmStatus extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "crm_status";
    protected $fillable = [
        'id_client', 'status'
    ];


    
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }
}
