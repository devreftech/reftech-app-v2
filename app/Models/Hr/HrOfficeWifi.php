<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrOfficeWifi extends Model
{
    use HasFactory;

    protected $table = 'hr_office_wifis';

    protected $fillable = [
        'name',
        'ip_address',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
