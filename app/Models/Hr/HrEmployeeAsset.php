<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEmployeeAsset extends Model
{
    use HasFactory;

    protected $table = 'hr_employee_assets';

    protected $fillable = [
        'employee_id',
        'fixed_asset_id',
        'asset_name',
        'asset_code',
        'serial_number',
        'condition',
        'handover_date',
        'returned_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'handover_date' => 'date',
        'returned_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function fixedAsset()
    {
        return $this->belongsTo(\App\Models\FixedAsset::class, 'fixed_asset_id');
    }
}
