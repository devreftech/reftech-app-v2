<?php

namespace Database\Seeders;

use App\Models\SalesReports;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesReportsTableSeeder extends Seeder
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
                'semester' => '1',
                'year' => '2023',
            ],
            [
                'semester' => '2',
                'year' => '2023',
            ],
            [
                'semester' => '1',
                'year' => '2024',
            ],
            [
                'semester' => '2',
                'year' => '2024',
            ],
            [
                'semester' => '1',
                'year' => '2025',
            ],
            [
                'semester' => '2',
                'year' => '2025',
            ],
            [
                'semester' => '1',
                'year' => '2026',
            ],
            [
                'semester' => '2',
                'year' => '2026',
            ],
            [
                'semester' => '1',
                'year' => '2027',
            ],
            [
                'semester' => '2',
                'year' => '2027',
            ],
        ];
        SalesReports::insert($reports);
    }
}
