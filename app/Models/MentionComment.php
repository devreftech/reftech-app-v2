<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentionComment extends Model
{
    use HasFactory;
    
    protected $table = "mention_comment";
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_comment',
        'id_mention',
        'level'
    ];
    public function comment()
    {
        return $this->belongsTo('App\Models\Comment', 'id_comment', 'id');
    }
    public function mention()
    {
        return $this->belongsTo('App\Models\User', 'id_mention', 'id');
    }
}
