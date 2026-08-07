<?php

namespace Database\Seeders;

use App\Models\Reports;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reports = [
            [
                'id_technician' => '9',
                'id_pic' => '1',
                'id_machine' => '2',
                'no_service' => '001-S/CWU/I/2024',
                'type' => 'Service',
                'unit' => 'Kaeser ASD',
                'serial_number' => '-',
                'running' => '3500',
                'load' => '4200',
                'jobdesc' => 'Membersihkan Kompressor',
                'desc' => 'Kompressor sudah berjalan dengan baik',
                'date' => Carbon::now(),
            ],
        ];
        Reports::insert($reports);
    }
}
