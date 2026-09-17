<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEvaluation extends Model
{
    use HasFactory;

    protected $table = 'hr_evaluations';

    protected $fillable = [
        'employee_id',
        'evaluator_id',
        'evaluation_type',
        'evaluation_date',
        'score',
        'strengths',
        'improvements',
        'recommendation',
        'status',
        'notes',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'score' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
