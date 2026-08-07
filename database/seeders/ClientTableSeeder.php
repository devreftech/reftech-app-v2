<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = [
            [
                'id_sales' => '1',
                'id_issues' => '1',
                'company' => 'PT. Rakha Daksa',
                'email' => 'rakshaDasha@gmail.com',
                'phone' => '0813875545221',
                'ru' => 'User',
                'web' => 'RakshaD.com',
                'image' => 'RakshaD.jpg',
                'source' => 'Instagram',
                'created_date'        => date('Y-m-d h:i:s'),
                'role' => 'Leads',
                'mobile' => 'whatsApp',
                'machine' => 'Atlas Copco',
                'address' => 'Babakan Ciparay, Bandung, Kelurahan Babakan Tarogong, Kecamatan Mana',
                'subAddress' => 'Babakan Tarogong, Bandung, Kelurahan Babakan Tarogong, Kecamatan Mana',
                'area' => 'Bandung',
                'note' => '-',
                'created_at'        => date('Y-m-d h:i:s'),
                'updated_at'        => date('Y-m-d h:i:s'),
            ],
            [
                'id_sales' => '2',
                'id_issues' => '2',
                'company' => 'PT. Indospring Tbk.',
                'email' => 'Indospring@gmail.com',
                'phone' => '0813875545221',
                'ru' => 'User',
                'web' => 'indospring.com',
                'image' => 'indospring.png',
                'source' => 'Instagram',
                'created_date'        => date('Y-m-d h:i:s'),
                'role' => 'Leads',
                'mobile' => 'whatsApp',
                'machine' => 'Kaeser',
                'address' => 'Jl BSD City, No 15J, Jakarta',
                'subAddress' => 'Jl Katapang City, No 15J, Jakarta',
                'area' => 'Jakarta',
                'note' => '-',
                'created_at'        => date('Y-m-d h:i:s'),
                'updated_at'        => date('Y-m-d h:i:s'),
            ],
            [
                'id_sales' => '1',
                'id_issues' => '3',
                'company' => 'PT Abadi',
                'email' => 'Abadi@selamanya.com',
                'phone' => '08456123546',
                'ru' => 'Reseller',
                'web' => 'AbadiSelamanya.com',
                'image' => 'abadi.jpg',
                'source' => 'LinkedIn',
                'created_date'        => date('Y-m-d h:i:s'),
                'role' => 'Customers',
                'mobile' => 'Phone',
                'machine' => 'Atlas Copco',
                'address' => 'Taman Kopo Indah V, Komplek Rousville No 18B, Bandung',
                'subAddress' => 'Taman Kopo Indah II, Komplek Rousville No 18B, Bandung',
                'area' => 'Bandung',
                'note' => '-',
                'created_at'        => date('Y-m-d h:i:s'),
                'updated_at'        => date('Y-m-d h:i:s'),
            ],
        ];

        Client::insert($client);
    }
}
