<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolAssignmentTechnician extends Model
{
    use HasFactory;
    protected $table = "tool_assignment_technicians";
    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
