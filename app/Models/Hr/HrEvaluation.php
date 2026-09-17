<?php

namespace App\Models\Hr;

use App\Models\User;
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

    public function getRatingLabelAttribute()
    {
        if ($this->score >= 90) return 'A+ (Sangat Unggul)';
        if ($this->score >= 80) return 'A (Memuaskan)';
        if ($this->score >= 70) return 'B (Sesuai Harapan)';
        if ($this->score >= 60) return 'C (Perlu Peningkatan)';
        return 'D (Di Bawah Standar)';
    }
}
