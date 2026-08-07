<?php

namespace Database\Seeders;

use App\Models\DetailQuotation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailQuotationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dQuotation = [
            [
                "id_quotation" => "1",
                "product"=> "Kaeseer ASD",
                "detail_product"=> "Parts Kaeser",
                "price"=> "300000",
                "qty"=> "2",
                "disc"=> "10",
                "amount"=> "600000",
                "fee"=> "0",
            ],
            [
                "id_quotation" => "1",
                "product"=> "Atlas copco",
                "detail_product"=> "Parts Atlas Copco PJ900",
                "price"=> "900000",
                "qty"=> "1",
                "disc"=> "10",
                "amount"=> "900000",
                "fee"=> "0",
            ],
            [
                "id_quotation" => "2",
                "product"=> "Kaeser",
                "detail_product"=> "Machine Kaeser AP3",
                "price"=> "3000000",
                "qty"=> "1",
                "disc"=> "10",
                "amount"=> "3000000",
                "fee"=> "0",
            ],
            [
                "id_quotation" => "3",
                "product" => "Kaeser",
                "detail_product"=> "Parts Machine Kaeser CK0125",
                "price"=> "500000",
                "qty"=> "3",
                "disc"=> "10",
                "amount"=> "1500000",
                "fee"=> "0",
            ],
            [
                "id_quotation" => "4",
                "product"=> "Atlas copco",
                "detail_product"=> "Machine Atlas Copco ACM25",
                "price"=> "25000000",
                "qty"=> "2",
                "disc"=> "10",
                "amount"=> "50000000",
                "fee"=> "0",
            ],
            [
                "id_quotation" => "5",
                "product"=> "Atlas copco",
                "detail_product"=> "Machine Atlas Copco ACM050",
                "price"=> "50000000",
                "qty"=> "4",
                "disc"=> "5",
                "amount"=> "200000000",
                "fee"=> "0",
            ],
        ];
        DetailQuotation::insert($dQuotation);
    }
}
