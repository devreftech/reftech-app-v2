<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = "service";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_client',
        'id_technician',
        'id_quotation',
        'no_prucashe',
        'job_description',
        'compressor_type',
        'area',
        'recommendation',
        'status'
    ];
    
    public function client()
    {
        return $this->hasMany('App\Model\client', 'id_service');
    }

    
    public function quotation()
    {
        return $this->hasMany('App\Model\Quotation', 'id_service');
    }
    
    
}
