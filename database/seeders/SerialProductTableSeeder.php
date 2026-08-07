<?php

namespace Database\Seeders;

use App\Models\SerialProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SerialProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sProduct = [
            [
                'id_product' => '1',
                'fxp_parts' => 'FX-8747601',
                'brand' => 'Kaeser',
                'pn' => '6.1981.0',
                'image' => 'image',
                'price' => '750000',
            ],
            [
                'id_product' => '1',
                'fxp_parts' => 'FX-8747602',
                'brand' => 'Atlas Copco',
                'pn' => '2901500540',
                'image' => 'image',
                'price' => '650000',
            ],
            [
                'id_product' => '1',
                'fxp_parts' => 'FX-8747605',
                'brand' => 'Kaeser',
                'pn' => '6.1981.10010',
                'image' => 'image',
                'price' => '675000',
            ],
            [
                'id_product' => '2',
                'fxp_parts' => 'FX-8747606',
                'brand' => 'Kaeser',
                'pn' => 'ZS1618471',
                'image' => 'image',
                'price' => '750000',
            ],
            [
                'id_product' => '2',
                'brand' => 'Compair',
                'fxp_parts' => 'FX-8747609',
                'pn' => '6.1985.0',
                'image' => 'image',
                'price' => '650000',
            ],
            [
                'id_product' => '2',
                'brand' => 'Kaeser',
                'fxp_parts' => 'FX-8747607',
                'pn' => '6.1985.1',
                'image' => 'image',
                'price' => '675000',
            ],
        ];
        SerialProduct::insert($sProduct);
    }
}
