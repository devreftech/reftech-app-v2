<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolAuditItem extends Model
{
    use HasFactory;
    protected $table = "tool_audit_item";
    protected $fillable = [
        'id_tool_audit',
        'id_fixed_asset',
        'qty_actual',
        'kondisi',
        'foto_audit',
        'alasan',
        'metode_ganti',
        'status_verifikasi_item',
        'catatan_admin_item',
    ];

    public function audit()
    {
        return $this->belongsTo('App\Models\ToolAudit', 'id_tool_audit', 'id');
    }
    public function fixedAsset()
    {
        return $this->belongsTo('App\Models\FixedAsset', 'id_fixed_asset', 'id');
    }
}
