<?php

namespace Database\Seeders;

use App\Models\DetailProductOut;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailProductOutTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dProduct = [
            [
                'id_detail_product' => '1',
                'id_serial_product' => '1',
                'id_product_out' => '2',
                'qty' => '6',
                'price' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '2',
                'id_serial_product' => '1',
                'id_product_out' => '2',
                'qty' => '6',
                'price' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '3',
                'id_serial_product' => '1',
                'id_product_out' => '2',
                'qty' => '6',
                'price' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '2',
                'id_serial_product' => '1',
                'id_product_out' => '1',
                'qty' => '6',
                'price' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '6',
                'id_serial_product' => '1',
                'id_product_out' => '2',
                'qty' => '6',
                'price' => '500000',
                'amount' => '3000000',
            ],
        ];
        DetailProductOut::insert($dProduct);
    }
}
