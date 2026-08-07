<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentionNotulen extends Model
{
    use HasFactory;
    
    protected $table = "mention_notulen";
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_notulen',
        'id_mention',
        'level'
    ];
    public function notuler()
    {
        return $this->belongsTo('App\Models\Notulen', 'id_notulen', 'id');
    }
    public function mention()
    {
        return $this->belongsTo('App\Models\User', 'id_mention', 'id');
    }
}
