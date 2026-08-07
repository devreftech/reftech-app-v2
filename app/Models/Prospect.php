<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;
    
    protected $table = "prospect";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_support',
        'id_sales',
        'id_quotation',
        'id_pic',
        'kebutuhan',
        'provide',
        'level'
    ];

    // Connection Table
    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }
    public function sales()
    {
        return $this->belongsTo('App\Models\User', 'id_sales', 'id');
    }
    public function support()
    {
        return $this->belongsTo('App\Models\User', 'id_support', 'id');
    }
    public function pic()
    {
        return $this->belongsTo('App\Models\Pic', 'id_pic', 'id');
    }
    public function Comment()
    {
        return $this->hasMany('App\Models\Comment', 'id_prospect');
    }
}
