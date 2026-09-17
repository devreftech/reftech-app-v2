<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\HrLeaveType;
use App\Models\HrLeaveBalance;
use App\Models\HrSalary;
use Illuminate\Database\Seeder;

class HrMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Master Leave Types
        $leaveTypes = [
            [
                'name' => 'Cuti Tahunan',
                'code' => 'CT',
                'default_days' => 12,
                'is_paid' => true,
                'requires_attachment' => false,
                'description' => 'Hak cuti tahunan reguler karyawan (sesuai UU Ketenagakerjaan).',
            ],
            [
                'name' => 'Izin Sakit',
                'code' => 'CS',
                'default_days' => 14,
                'is_paid' => true,
                'requires_attachment' => true,
                'description' => 'Izin sakit dengan melampirkan surat keterangan dokter resmi.',
            ],
            [
                'name' => 'Cuti Melahirkan',
                'code' => 'CM',
                'default_days' => 90,
                'is_paid' => true,
                'requires_attachment' => true,
                'description' => 'Hak cuti melahirkan bagi karyawan wanita.',
            ],
            [
                'name' => 'Cuti Menikah',
                'code' => 'CN',
                'default_days' => 3,
                'is_paid' => true,
                'requires_attachment' => false,
                'description' => 'Cuti khusus pernikahan karyawan.',
            ],
            [
                'name' => 'Cuti Duka Cita',
                'code' => 'CD',
                'default_days' => 2,
                'is_paid' => true,
                'requires_attachment' => false,
                'description' => 'Cuti keluarga inti (orang tua/anak/pasangan) meninggal dunia.',
            ],
            [
                'name' => 'Izin Tanpa Upah (Unpaid Leave)',
                'code' => 'UL',
                'default_days' => 5,
                'is_paid' => false,
                'requires_attachment' => false,
                'description' => 'Izin keperluan pribadi di luar kuota cuti tahunan.',
            ],
        ];

        foreach ($leaveTypes as $type) {
            HrLeaveType::firstOrCreate(['code' => $type['code']], $type);
        }

        // 2. Seed Leave Balances 2026 for existing employees
        $employees = Employee::all();
        $currentYear = (int) date('Y');

        foreach ($employees as $emp) {
            HrLeaveBalance::firstOrCreate(
                ['employee_id' => $emp->id, 'year' => $currentYear],
                [
                    'total_quota' => 12,
                    'used_quota' => 0,
                    'remaining_quota' => 12,
                ]
            );

            // 3. Seed default base salary structure if not yet set
            HrSalary::firstOrCreate(
                ['employee_id' => $emp->id],
                [
                    'basic_salary' => 5500000.00,
                    'transport_allowance' => 500000.00,
                    'meal_allowance' => 650000.00,
                    'position_allowance' => 750000.00,
                    'other_allowance' => 0.00,
                    'bpjs_kesehatan' => 55000.00,
                    'bpjs_ketenagakerjaan' => 110000.00,
                    'bank_name' => 'BCA',
                    'bank_account_number' => '8273' . str_pad($emp->id, 6, '0', STR_PAD_LEFT),
                    'bank_account_holder' => $emp->user?->name ?? ($emp->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $emp->id),
                    'effective_date' => $emp->join_date ?? date('Y-m-d'),
                ]
            );
        }
    }
}
