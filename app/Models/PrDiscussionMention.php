<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrDiscussionMention extends Model
{
    use HasFactory;

    protected $table = 'pr_discussion_mention';
    protected $fillable = ['id_discussion', 'id_user_mention', 'level'];

    public function discussion()
    {
        return $this->belongsTo(PrDiscussion::class, 'id_discussion');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_mention');
    }
}
