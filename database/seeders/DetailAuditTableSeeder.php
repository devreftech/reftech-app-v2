<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\DetailAudit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailAuditTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $detailA = [
            [
                'id_technician' => '8',
                'no_audit' => 'TMU/I/2024',
                'note' => '-',
                'Status' => 'OK',
                'date' => \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
            ],
            [
                'id_technician' => '9',
                'no_audit' => 'CWA/I/2024',
                'note' => '-',
                'Status' => 'OK',
                'date' => \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
            ],
            [
                'id_technician' => '10',
                'no_audit' => 'TSU/I/2024',
                'note' => '-',
                'Status' => 'OK',
                'date' => \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
            ],
        ];
        Audit::insert($detailA);
    }
}
