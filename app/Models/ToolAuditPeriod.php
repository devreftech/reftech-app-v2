<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getSemesterLabelAttribute()
    {
        $map = [
            1 => 'Triwulan I (Q1 - Maret)',
            2 => 'Triwulan II (Q2 - Juni)',
            3 => 'Triwulan III (Q3 - September)',
            4 => 'Triwulan IV (Q4 - Desember)',
        ];
        return $map[$this->semester] ?? "Triwulan {$this->semester}";
    }

    public function getShortSemesterLabelAttribute()
    {
        $map = [
            1 => 'Q1 (Triwulan I)',
            2 => 'Q2 (Triwulan II)',
            3 => 'Q3 (Triwulan III)',
            4 => 'Q4 (Triwulan IV)',
        ];
        return $map[$this->semester] ?? "Q{$this->semester}";
    }

    public function getPeriodTitleAttribute()
    {
        return $this->tahun . ' - ' . $this->getShortSemesterLabelAttribute();
    }

    public function getIsDateActiveAttribute()
    {
        $today = Carbon::today()->toDateString();
        return $this->tanggal_mulai <= $today && $this->tanggal_selesai >= $today;
    }
}

