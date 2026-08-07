<?php

namespace Database\Seeders;

use App\Models\RevQuote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RevQuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $revQ = [
            [
                "id_quotation"=> "2",
                "no_pr"=> "RKDS-PR-122-1-2023",
                "rev_no_quote"=> "RJO-XIII-2023-122-REV-1",
                "discount"=> "100000",
                "total_after_disc"=> "3010000",
            ],
            [
                "id_quotation"=> "2",
                "no_pr"=> "RKDS-PR-122-2-2023",
                "rev_no_quote"=> "RJO-XIII-2023-122-REV-2",
                "discount"=> "110000",
                "total_after_disc"=> "3000000",
            ],
            [
                "id_quotation"=> "3",
                "no_pr"=> "INDSP-PR-125-1-2023",
                "rev_no_quote"=> "RJO-XIII-2023-125-REV-1",
                "discount"=> "710000",
                "total_after_disc"=> "1500000",
            ],
        ];
        RevQuote::insert($revQ);
    }
}
