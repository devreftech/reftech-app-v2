<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskTicket extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'helpdesk_tickets';

    protected $fillable = [
        'no_ticket',
        'id_user',
        'category',
        'title',
        'description',
        'error_file',
        'error_line',
        'error_exception',
        'url_accessed',
        'http_method',
        'status',
        'resolution_note',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
