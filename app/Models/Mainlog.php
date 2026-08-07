<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mainlog extends Model
{
    use HasFactory;
    protected $table = "main_log";
    protected $date = [
        'created_at',
        'updated_at',
        'date'
    ];
    protected $fillable = [
        'desc',
        'next',
    ];
    
    public function machine()
    {
        return $this->belongsTo('App\Models\Machine', 'id_machine', 'id');
    }
    
    public function issue()
    {
        return $this->belongsTo('App\Models\Monitoring', 'id_issue', 'id');
    }
    public function teknisi()
    {
        return $this->belongsTo('App\Models\User', 'id_teknisi', 'id');
    }
}
