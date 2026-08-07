<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    
    protected $table = "comment";
    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_user',
        'id_status',
        'id_prospect',
        'comment',
        'level'
    ];
    public function status()
    {
        return $this->belongsTo('App\Models\ChangeStatus', 'id_status', 'id');
    }
    public function prospect()
    {
        return $this->belongsTo('App\Models\Prospect', 'id_prospect', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
    
    public function mention()
    {
        return $this->hasMany('App\Models\MentionComment', 'id_comment');
    }
}
