<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseBudget extends Model
{
    use HasFactory;

    protected $table = 'expense_budgets';

    protected $fillable = [
        'year',
        'entity',
        'annual_budget',
        'monthly_budget',
        'department_budgets',
        'm1',
        'm2',
        'm3',
        'm4',
        'm5',
        'm6',
        'm7',
        'm8',
        'm9',
        'm10',
        'm11',
        'm12',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'year'               => 'integer',
        'annual_budget'      => 'double',
        'monthly_budget'     => 'double',
        'department_budgets' => 'array',
        'm1'                 => 'double',
        'm2'                 => 'double',
        'm3'                 => 'double',
        'm4'                 => 'double',
        'm5'                 => 'double',
        'm6'                 => 'double',
        'm7'                 => 'double',
        'm8'                 => 'double',
        'm9'                 => 'double',
        'm10'                => 'double',
        'm11'                => 'double',
        'm12'                => 'double',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Definisi Master Sub-Departemen / Kategori Utama Budget Perusahaan
     */
    public static function getDepartmentDefinitions(): array
    {
        return [
            'operasional' => [
                'key'   => 'operasional',
                'name'  => 'Operasional & Fasilitas',
                'short' => 'Operasional',
                'icon'  => 'mdi-domain',
                'color' => '#03c3ec',
                'badge' => 'bg-label-info',
                'desc'  => 'Listrik, Internet, Perjalanan Dinas, Pemeliharaan Kendaraan, ATK & Pengiriman',
            ],
            'marketing' => [
                'key'   => 'marketing',
                'name'  => 'Marketing & Penjualan',
                'short' => 'Marketing',
                'icon'  => 'mdi-bullhorn-outline',
                'color' => '#696cff',
                'badge' => 'bg-label-primary',
                'desc'  => 'Pemasaran, Iklan, Promosi, Layanan Aplikasi Ecommerce & Packing',
            ],
            'hr_payroll' => [
                'key'   => 'hr_payroll',
                'name'  => 'SDM & Payroll (HR)',
                'short' => 'SDM / HR',
                'icon'  => 'mdi-account-group-outline',
                'color' => '#71dd37',
                'badge' => 'bg-label-success',
                'desc'  => 'Gaji & Upah, THR, Bonus, Lembur, BPJS Kesehatan & Ketenagakerjaan',
            ],
            'umum_legal' => [
                'key'   => 'umum_legal',
                'name'  => 'Umum, Pajak & Legal',
                'short' => 'Umum & Legal',
                'icon'  => 'mdi-scale-balance',
                'color' => '#ffab00',
                'badge' => 'bg-label-warning',
                'desc'  => 'Sewa Gedung, Asuransi, Pajak PPh (21/25/29), Administrasi Bank & CSR',
            ],
            'depresiasi' => [
                'key'   => 'depresiasi',
                'name'  => 'Penyusutan Aset (Depresiasi)',
                'short' => 'Penyusutan',
                'icon'  => 'mdi-trending-down',
                'color' => '#8592a3',
                'badge' => 'bg-label-secondary',
                'desc'  => 'Penyusutan Bangunan, Mesin, Kendaraan, Tools & Peralatan Kantor',
            ],
        ];
    }

    /**
     * Map any COA Account code/name to its appropriate Department key.
     */
    public static function mapAccountToDepartment(?string $code, ?string $name): string
    {
        $code = (string) $code;
        $name = strtolower((string) $name);

        // 1. Depresiasi / Penyusutan
        if (str_starts_with($code, '6100') || str_contains($name, 'penyusutan') || str_contains($name, 'depresiasi')) {
            return 'depresiasi';
        }

        // 2. Marketing & Penjualan
        if (
            in_array($code, ['6200-005', '6200-029', '6200-030', '6200-031']) ||
            str_contains($name, 'pemasaran') ||
            str_contains($name, 'iklan') ||
            str_contains($name, 'marketing') ||
            str_contains($name, 'ecommerce') ||
            str_contains($name, 'promosi')
        ) {
            return 'marketing';
        }

        // 3. SDM & Payroll (HR)
        if (
            in_array($code, ['6200-002', '6200-003', '6200-004', '6200-013', '6200-014', '6200-023', '6200-024', '6200-025', '6200-034']) ||
            str_contains($name, 'gaji') ||
            str_contains($name, 'upah') ||
            str_contains($name, 'thr') ||
            str_contains($name, 'bonus') ||
            str_contains($name, 'lembur') ||
            str_contains($name, 'overtime') ||
            str_contains($name, 'bpjs') ||
            str_contains($name, 'kesehatan karyawan') ||
            str_contains($name, 'tunjangan')
        ) {
            return 'hr_payroll';
        }

        // 4. Umum, Pajak & Legal
        if (
            in_array($code, ['6200-006', '6200-007', '6200-008', '6200-012', '6200-020', '6200-021', '6200-022', '7200-001', '7200-002', '7200-003', '7500']) ||
            str_contains($name, 'pajak') ||
            str_contains($name, 'pph') ||
            str_contains($name, 'asuransi') ||
            str_contains($name, 'sewa') ||
            str_contains($name, 'administrasi bank') ||
            str_contains($name, 'csr') ||
            str_contains($name, 'legal')
        ) {
            return 'umum_legal';
        }

        // 5. Default ke Operasional & Fasilitas
        return 'operasional';
    }

    /**
     * Get budget for a specific month (1 - 12).
     * Falls back to monthly_budget if specific month value is null.
     */
    public function getBudgetForMonth(int $month): float
    {
        $col = 'm' . $month;
        if (isset($this->$col) && !is_null($this->$col) && $this->$col > 0) {
            return (float) $this->$col;
        }

        return (float) ($this->monthly_budget > 0 ? $this->monthly_budget : round($this->annual_budget / 12));
    }

    /**
     * Get budget settings for a specific department
     */
    public function getDepartmentBudget(string $deptKey): array
    {
        $deptBudgets = $this->department_budgets ?? [];
        if (isset($deptBudgets[$deptKey])) {
            $annual = (float) ($deptBudgets[$deptKey]['annual'] ?? 0);
            $monthly = (float) ($deptBudgets[$deptKey]['monthly'] ?? ($annual > 0 ? round($annual / 12) : 0));
            return [
                'annual'  => $annual,
                'monthly' => $monthly,
            ];
        }

        return [
            'annual'  => 0,
            'monthly' => 0,
        ];
    }
}
