<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSemesterBonusItem extends Model
{
    use HasFactory;

    protected $table = 'hr_semester_bonus_items';

    protected $fillable = [
        'recipient_id',
        'name',
        'type',
        'amount',
        'notes',
        'order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'order' => 'integer',
    ];

    public function recipient()
    {
        return $this->belongsTo(HrSemesterBonusRecipient::class, 'recipient_id');
    }
}
