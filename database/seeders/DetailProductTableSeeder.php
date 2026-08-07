<?php

namespace Database\Seeders;

use App\Models\DetailProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailProductTableSeeder extends Seeder
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
                'id_product' => '1',
                'replacement' => 'Jimco JOC-88033',
                'modal' => '75000',
                'warehouse_stock' => '0',
                'stock' => '6',
            ],
            [
                'id_product' => '1',
                'replacement' => 'Sure Filter SFO-7874',
                'modal' => '78000',
                'warehouse_stock' => '0',
                'stock' => '3',
            ],
            [
                'id_product' => '1',
                'replacement' => 'Mann W962',
                'modal' => '98000',
                'warehouse_stock' => '0',
                'stock' => '1',
            ],
            [
                'id_product' => '2',
                'replacement' => 'Jimco JOC-88041',
                'modal' => '65000',
                'warehouse_stock' => '0',
                'stock' => '2',
            ],
            [
                'id_product' => '2',
                'replacement' => 'Sure Filter SFO-7870',
                'modal' => '68000',
                'warehouse_stock' => '0',
                'stock' => '1',
            ],
            [
                'id_product' => '2',
                'replacement' => 'Mann W940',
                'modal' => '105000',
                'warehouse_stock' => '0',
                'stock' => '1',
            ],
        ];
        DetailProduct::insert($dProduct);
    }
}
