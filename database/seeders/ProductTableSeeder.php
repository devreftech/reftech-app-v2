<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
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
                'commodity' => 'W962',
                'description' => 'Oil Filter',
                'detail_desc' => 'Oil Filter',
                'go' => 'Oem',
                'category' => 'Consumable Part',
                'dimension' => '200x100x150',
                'first_stock' => '10',
                'warehouse_stock' => '10',
                'stock' => '10',
                'weight' => '10',
                'unit' => 'pail',
                'note' => '-',
                "date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
            ],
            [
                'commodity' => 'W940',
                'description' => 'Oil Filter',
                'detail_desc' => 'Oil Filter',
                'go' => 'Oem',
                'category' => 'Consumable Part',
                'dimension' => '170x100x150',
                'first_stock' => '4',
                'warehouse_stock' => '4',
                'stock' => '4',
                'weight' => '10',
                'unit' => 'pail',
                'note' => '-',
                "date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
            ],
        ];
        Product::insert($product);
    }
}
