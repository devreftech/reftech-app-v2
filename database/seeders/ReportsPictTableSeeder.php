<?php

namespace Database\Seeders;

use App\Models\ReportsPict;
use Illuminate\Database\Seeder;

class ReportsPictTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reportspict = [
            [
                'id_reports' => '1',
                'picture' => 'asset/profile/profile.jpg',
                'keterangan' => 'poto 1',
            ],
            [
                'id_reports' => '1',
                'picture' => 'asset/profile/profile.jpg',
                'keterangan' => 'poto 2',
            ],
            [
                'id_reports' => '1',
                'picture' => 'asset/profile/profile.jpg',
                'keterangan' => 'poto 3',
            ],
            [
                'id_reports' => '1',
                'picture' => 'asset/profile/profile.jpg',
                'keterangan' => 'poto 4',
            ],
        ];
        ReportsPict::insert($reportspict);
    }
}
