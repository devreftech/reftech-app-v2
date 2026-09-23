<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineLeadFollowUp extends Model
{
    protected $table = 'online_lead_follow_ups';

    protected $fillable = [
        'id_lead',
        'id_user',
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(OnlineLead::class, 'id_lead');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function items()
    {
        return $this->hasMany(OnlineLeadFollowUpItem::class, 'id_follow_up');
    }

    public function getProvidedCountAttribute(): int
    {
        return $this->items->where('status', 'provided')->count();
    }

    public function getTotalItemsCountAttribute(): int
    {
        return $this->items->count();
    }
}
