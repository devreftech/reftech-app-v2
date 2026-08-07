<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    use HasFactory;
    
    protected $table = "notulen";
    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_notuler',
        'title',
        'desc',
        'level'
    ];
    public function notuler()
    {
        return $this->belongsTo('App\Models\User', 'id_notuler', 'id');
    }
    public function mention()
    {
        return $this->hasMany('App\Models\MentionNotulen', 'id_notulen');
    }
}
