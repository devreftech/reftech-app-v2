<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrLeaveType extends Model
{
    use HasFactory;

    protected $table = 'hr_leave_types';

    protected $fillable = [
        'name',
        'code',
        'default_days',
        'is_paid',
        'requires_attachment',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_attachment' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(HrLeaveRequest::class, 'leave_type_id');
    }
}
