<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = "client";
    protected $dates = [
        'created_date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_sales',
        'id_pic',
        'id_issues',
        'id_service',
        'company',
        'email',
        'phone',
        'ru',
        'web',
        'image',
        'source',
        'source_detail',
        'role',
        'mobile',
        'address',
        'subAddress',
        'area'
    ];

    // Connection Table
    public function sales()
    {
        return $this->belongsTo('App\Models\User', 'id_sales', 'id');
    }
    public function issues()
    {
        return $this->belongsTo('App\Models\Issues', 'id_issues', 'id');
    }
    public function service()
    {
        return $this->belongsTo('App\Models\Service', 'id_service', 'id');
    }

    // Extend Table

    public function activities()
    {
        return $this->hasMany('App\Models\Activities', 'id_client');
    }

    public function detail_client()
    {
        return $this->hasMany('App\Models\Detailclient', 'id_client');
    }


    public function visit()
    {
        return $this->hasMany('App\Models\Visit', 'id_client');
    }
    public function pic()
    {
        return $this->hasMany('App\Models\PIC', 'id_client');
    }
    public function machine()
    {
        return $this->hasMany('App\Models\Machine', 'id_client');
    }
    public function plants()
    {
        return $this->hasMany('App\Models\ClientPlant', 'id_client');
    }
    public function crmStatus()
    {
        return $this->hasMany(CrmStatus::class, 'id_client');
    }

}
