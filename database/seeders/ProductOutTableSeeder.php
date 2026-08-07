<?php

namespace Database\Seeders;

use App\Models\ProductOut;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductOutTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = [
            [
                'id_user' => '1',
                'detail_client' => 'Bpk Asep',
                'invoice' => 'PIN002/14/III/23',
                "date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                'note' => '-',
                'vers' => 'Online',
                'shipping' => '500000',
                'total' => '5000000',
            ],
            [
                'id_user' => '2',
                'detail_client' => 'Bpk Raden Dewi',
                'invoice' => 'PIN002/14/III/23',
                "date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                'note' => '-',
                'vers' => 'Online',
                'shipping' => '250000',
                'total' => '2500000',
            ],
        ];
        ProductOut::insert($product);
    }
}
