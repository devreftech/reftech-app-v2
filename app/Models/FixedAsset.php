<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    protected $table = "fixed_asset";
    protected $date = [
        'date',
        'pakai',
        'bayar',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'type',
        'code',
        'no_invoice',
        'metode',
        'desc',
        'qty',
        'status',
        'total',
        'id_unit',
        'serial_number',
        'kondisi',
        'qc_status',
        'status_unit',
        'harga_jual',
        'id_machine',
        'confirmed_by',
        'confirmed_at',
        'mulai_penyusutan',
        'jenis_kendaraan',
        'merk_model',
        'bahan_bakar',
        'plat_nomor',
        'atas_nama',
        'id_tools_master',
        'id_pic',
        'foto_awal',
        'tanggal_serah_terima',
        'status_tools',
        'is_disposed',
        'tanggal_disposal',
        'nilai_buku_disposal',
        'harga_jual_final',
    ];

    /**
     * Garis lurus, 25%/tahun hardcoded, dicap maksimal `umur` bulan. Basis nilai
     * adalah `total` (sudah termasuk kapitalisasi servis). Unit "Dalam Pengecekan"
     * belum boleh disusutkan sama sekali. Diekstrak dari FixedController::show()
     * supaya bisa dipakai ulang saat disposal (unit keluar) tanpa duplikasi logic.
     */
    public function hitungNilaiBuku(): array
    {
        if ($this->qc_status === 'checking') {
            return ['total_penyusutan' => 0, 'nilai_buku' => $this->total];
        }

        $startDate = Carbon::parse($this->mulai_penyusutan ?? $this->beli);
        $endDate = Carbon::now();
        $diffMonth = $startDate->greaterThan($endDate) ? 0 : $startDate->diffInMonths($endDate);
        $bulanPenyusutan = min($diffMonth, $this->umur);
        $penyusutanPerBulan = ($this->total * 0.25) / 12;
        $totalPenyusutan = $penyusutanPerBulan * $bulanPenyusutan;
        $nilaiBuku = $this->total - $totalPenyusutan;

        return ['total_penyusutan' => $totalPenyusutan, 'nilai_buku' => $nilaiBuku];
    }

    public function aktiva()
    {
        return $this->belongsTo('App\Models\Account', 'id_aktiva', 'id');
    }
    public function penyusutan()
    {
        return $this->belongsTo('App\Models\Account', 'id_penyusutan', 'id');
    }
    public function beban()
    {
        return $this->belongsTo('App\Models\Account', 'id_beban', 'id');
    }
    public function pengeluaran()
    {
        return $this->belongsTo('App\Models\Account', 'id_pengeluaran', 'id');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id_unit', 'id');
    }
    public function confirmedBy()
    {
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }
    public function services()
    {
        return $this->hasMany('App\Models\FixedAssetService', 'id_fixed_asset');
    }
    public function machine()
    {
        return $this->belongsTo('App\Models\Machine', 'id_machine', 'id');
    }
    public function maintenanceLogs()
    {
        return $this->hasMany('App\Models\VehicleMaintenanceLog', 'id_fixed_asset')->orderByDesc('tanggal');
    }
    public function toolsMaster()
    {
        return $this->belongsTo('App\Models\ToolMaster', 'id_tools_master', 'id');
    }
    public function pic()
    {
        return $this->belongsTo('App\Models\User', 'id_pic', 'id');
    }
}
