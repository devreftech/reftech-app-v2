<?php

namespace Database\Seeders;

use App\Models\Visit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $visit = [
            [
                'id_client' => 2,
                'status' => 'done',
                'compressor_data' => 'berhasil',
                'running_hour' => 2,
                'date' => \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                'prospect' => '-',
                'map_url' => 'https://maps.app.goo.gl/meiFD17NNynf5u2E9',
                'note' => 'Compressor berhasil',
                'recomendation' => 'Upgrade supaya lebih baik',
            ],
            [
                'id_client' => 3,
                'status' => 'done',
                'compressor_data' => 'berhasil',
                'running_hour' => 2,
                'date' => \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                'prospect' => '-',
                'map_url' => 'https://maps.app.goo.gl/hXyrhote2Kfn7qkz7',
                'note' => 'Tidak Berjalan Sempurna',
                'recomendation' => 'Ganti spare part agar lancar',
            ],
        ];
        Visit::insert( $visit );
    }
}
