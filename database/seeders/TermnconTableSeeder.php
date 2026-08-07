<?php

namespace Database\Seeders;

use App\Models\Termncon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermnconTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $termncon = [
            [
                "id_quotation"=> "1",
                "validity" =>"2 (two) weeks since the date of Quotation",
                "pricing"=> "Franco Factory ( BEKASI )",
                "delivery_process"=> "Ready Stock",
                "payment"=> "Cash before delivery",
            ],
            [
                "id_quotation"=> "2",
                "validity" =>"1(one) Month since the date of Quotation",
                "pricing"=> "Franco FACTORY",
                "delivery_process"=> "Item Indent 30-40 Days (Prices do not include shipping costs)",
                "payment"=> "50% DP, 50% Before delivery",
            ],
            [
                "id_quotation"=> "3",
                "validity" =>"3(three) Week since the date of Quotation",
                "pricing"=> "Franco FACTORY",
                "delivery_process"=> "Item Indent 20-30 Days (Prices include shipping costs)",
                "payment"=> "50% DP, 50% Before delivery",
            ],
            [
                "id_quotation"=> "4",
                "validity" =>"1(one) Month since the date of Quotation",
                "pricing"=> "Franco FACTORY ( BEKASI )",
                "delivery_process"=> "Ready Stock",
                "payment"=> "Before delivery",
            ],
            [
                "id_quotation"=> "5",
                "validity" =>"2 (two) weeks since the date of Quotation",
                "pricing"=> "Franco FACTORY ( BEKASI )",
                "delivery_process"=> "Item Indent 30-40 Days (Prices do not include shipping costs)",
                "payment"=> "Before delivery",
            ],
        ];
        Termncon::insert($termncon);
    }
}
