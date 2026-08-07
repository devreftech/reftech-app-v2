<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskTicket extends Model
{
    use HasFactory;

    protected $table = "helpdesk_tickets";
    protected $fillable = [
        'no_ticket',
        'id_user',
        'title',
        'description',
        'status',
        'resolution_note',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
