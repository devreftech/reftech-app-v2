<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectNotification extends Model
{
    use HasFactory;

    protected $table = 'prospect_notifications';

    protected $fillable = [
        'id_prospect',
        'id_user',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function prospect()
    {
        return $this->belongsTo('App\Models\Prospect', 'id_prospect', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
