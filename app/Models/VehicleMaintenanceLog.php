<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenanceLog extends Model
{
    use HasFactory;
    protected $table = "vehicle_maintenance_log";
    protected $fillable = [
        'id_fixed_asset',
        'jenis',
        'tanggal',
        'tanggal_jatuh_tempo',
        'biaya',
        'catatan',
        'created_by',
    ];
    public function fixedAsset()
    {
        return $this->belongsTo('App\Models\FixedAsset', 'id_fixed_asset', 'id');
    }
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
