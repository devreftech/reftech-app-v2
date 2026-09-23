<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineLeadFollowUpItem extends Model
{
    protected $table = 'online_lead_follow_up_items';

    protected $fillable = [
        'id_follow_up',
        'item_name',
        'qty',
        'status',
        'note',
    ];

    public const STATUSES = [
        'pending' => ['label' => 'Pending', 'badge' => 'secondary', 'icon' => 'mdi-clock-outline'],
        'provided' => ['label' => 'Provided', 'badge' => 'success', 'icon' => 'mdi-check-circle-outline'],
        'not_provided' => ['label' => 'Not Provided', 'badge' => 'danger', 'icon' => 'mdi-close-circle-outline'],
    ];

    public function followUp()
    {
        return $this->belongsTo(OnlineLeadFollowUp::class, 'id_follow_up');
    }

    public function getStatusMetaAttribute(): array
    {
        return self::STATUSES[$this->status] ?? self::STATUSES['pending'];
    }
}
