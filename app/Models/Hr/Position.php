<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'id_department',
        'name',
        'level',
        'is_active',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'id_department');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'id_position');
    }
}
