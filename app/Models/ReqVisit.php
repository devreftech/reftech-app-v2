<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqVisit extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "req_visit";
    protected $date = [
        'req_date',
        'visit_date',
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_machine',
        'id_service',
        'note',
        'visit_note',
        'desc',
        'status',
    ];

    
    public function machine()
    {
        return $this->belongsTo('App\Models\Machine', 'id_machine', 'id');
    }
}
