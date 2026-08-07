<?php

namespace Database\Seeders;

use App\Models\DetailProductIn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailProductInTableSeeder extends Seeder
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
                'id_product_in' => '2',
                'qty' => '6',
                'modal' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '2',
                'id_product_in' => '2',
                'qty' => '6',
                'modal' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '3',
                'id_product_in' => '2',
                'qty' => '6',
                'modal' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '2',
                'id_product_in' => '1',
                'qty' => '6',
                'modal' => '500000',
                'amount' => '3000000',
            ],
            [
                'id_detail_product' => '6',
                'id_product_in' => '2',
                'qty' => '6',
                'modal' => '500000',
                'amount' => '3000000',
            ],
        ];
        DetailProductIn::insert($dProduct);
    }
}
