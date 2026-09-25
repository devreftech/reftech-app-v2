<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use LogsActivity;

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
        'harga_rental_hari',
        'harga_rental_bulan',
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
        'lokasi_bangunan',
        'luas_bangunan',
        'luas_tanah',
        'status_bangunan',
        'tipe_pengadaan',
        'nomor_dokumen_legalitas',
        'pic_construction_ids',
    ];

    protected $casts = [
        'pic_construction_ids' => 'array',
    ];

    /**
     * Dapatkan koleksi User yang ditugaskan sebagai PIC Penginput Biaya Pembangunan.
     */
    public function getPicConstructionUsersAttribute()
    {
        $ids = $this->pic_construction_ids;
        if (empty($ids) || !is_array($ids)) {
            return collect();
        }
        return \App\Models\User::whereIn('id', $ids)->get();
    }

    /**
     * Garis lurus, dihitung berdasarkan `umur` bulan (default 48 bulan = 25%/thn, bangunan 240 bulan = 5%/thn).
     * Basis nilai adalah `total` (sudah termasuk kapitalisasi material/servis).
     * Unit "Dalam Pengecekan" atau Bangunan "Dalam Pembangunan (construction)" belum boleh disusutkan.
     */
    public function hitungNilaiBuku(): array
    {
        if ($this->qc_status === 'checking' || $this->status_bangunan === 'construction') {
            return ['total_penyusutan' => 0, 'nilai_buku' => (float) $this->total];
        }

        $startDate = Carbon::parse($this->mulai_penyusutan ?? $this->beli);
        $endDate = Carbon::now();
        $diffMonth = $startDate->greaterThan($endDate) ? 0 : $startDate->diffInMonths($endDate);
        $umurBulan = (int) ($this->umur ?: 48);
        $bulanPenyusutan = min($diffMonth, $umurBulan);
        $penyusutanPerBulan = $umurBulan > 0 ? ($this->total / $umurBulan) : (($this->total * 0.25) / 12);
        $totalPenyusutan = $penyusutanPerBulan * $bulanPenyusutan;
        $nilaiBuku = max(0, $this->total - $totalPenyusutan);

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
    public function rentalScans()
    {
        return $this->hasMany('App\Models\FixedAssetRentalScan', 'id_fixed_asset')->orderByDesc('id');
    }
    public function hrEmployeeAssets()
    {
        return $this->hasMany('App\Models\HrEmployeeAsset', 'fixed_asset_id')->orderByDesc('handover_date');
    }
    public function workOrders()
    {
        return $this->hasMany('App\Models\WorkOrder', 'id_fixed_asset')->orderByDesc('id');
    }
    public function constructionCosts()
    {
        return $this->hasMany(FixedAssetConstructionCost::class, 'fixed_asset_id')->orderByDesc('tanggal')->orderByDesc('id');
    }
}
