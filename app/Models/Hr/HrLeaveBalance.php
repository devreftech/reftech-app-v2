<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrLeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'hr_leave_balances';

    protected $fillable = [
        'employee_id',
        'year',
        'total_quota',
        'used_quota',
        'remaining_quota',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'total_quota' => 'integer',
        'used_quota' => 'integer',
        'remaining_quota' => 'integer',
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
