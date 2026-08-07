<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolAuditPeriod extends Model
{
    use HasFactory;
    protected $table = "tool_audit_period";
    protected $fillable = [
        'tahun',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    public function audits()
    {
        return $this->hasMany('App\Models\ToolAudit', 'id_audit_period');
    }
}
