<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolAudit extends Model
{
    use HasFactory;
    protected $table = "tool_audit";
    protected $fillable = [
        'id_audit_period',
        'id_technician',
        'no_audit',
        'status_submit',
        'submitted_at',
        'verified_by',
        'verified_at',
        'catatan_admin',
        'total_tools',
        'total_ada',
        'total_rusak',
        'total_hilang',
    ];

    public function period()
    {
        return $this->belongsTo('App\Models\ToolAuditPeriod', 'id_audit_period', 'id');
    }
    public function technician()
    {
        return $this->belongsTo('App\Models\User', 'id_technician', 'id');
    }
    public function verifiedBy()
    {
        return $this->belongsTo('App\Models\User', 'verified_by', 'id');
    }
    public function items()
    {
        return $this->hasMany('App\Models\ToolAuditItem', 'id_tool_audit');
    }
}
