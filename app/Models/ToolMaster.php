<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolMaster extends Model
{
    use HasFactory;
    protected $table = "tool_master";
    protected $fillable = [
        'nama_tools',
        'kategori',
        'spesifikasi',
        'foto_referensi',
        'link_pembelian',
        'harga_referensi',
        'status_aktif',
    ];

    public function instances()
    {
        return $this->hasMany('App\Models\FixedAsset', 'id_tools_master');
    }
}
