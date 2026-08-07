<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    use HasFactory;

    protected $table = "pic";
    protected $fillable = [
        'id_client',
        'name_pic',
        'position',
        'email_pic',
        'phone_pic',
    ];

    
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }
    
    public function quotation()
    {
        return $this->hasMany('App\Models\Quotation', 'id_pic');
    }
}
