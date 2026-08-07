<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issues extends Model
{
    use HasFactory;
    protected $table = "issues";
    protected $fillable = [
        'issue'
    ];

    
    public function client()
    {
        return $this->hasMany('App\Model\Client', 'id_issues');
    }
    
}
