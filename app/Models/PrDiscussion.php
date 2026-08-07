<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrDiscussion extends Model
{
    use HasFactory;

    protected $table = 'pr_discussion';
    protected $fillable = ['id_pending', 'id_user', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pending()
    {
        return $this->belongsTo(PendingPO::class, 'id_pending');
    }

    public function mentions()
    {
        return $this->hasMany(PrDiscussionMention::class, 'id_discussion');
    }
}
