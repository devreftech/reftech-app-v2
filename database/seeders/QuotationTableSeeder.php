<?php

namespace Database\Seeders;

use App\Models\Quotation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuotationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quotation = [
            [
                "id_pic"=> "1",
                "id_sales"=> "2",
                "id_service"=> NULL,
                "no_pr"=> NULL,
                "title"=> "Parts of Kaiser and Atlas Cocpo",
                "status"=> "25",
                "status_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "po_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "upload_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "note"=> "-",
                "flag"=> "Reftech",
                "estimated_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "expired_date"=> \Carbon\Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
                "no_quote"=> "RJO-XIII-2023-121",
                "tax"=> "12",
                "diskon"=> "0",
                "fee"=> "0",
                "nett"=> "1500000",
                "shipping"=> "50000",
                "subtotal"=> "1500000",
                "harga_total"=> "1750000",
            ],
            [
                "id_pic"=> "2",
                "id_sales"=> "2",
                "id_service"=> NULL,
                "no_pr"=> NULL,
                "title"=> "Machine Kaiser",
                "status"=> "50",
                "status_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "po_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "upload_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "note"=> "Quotation has been sended to company",
                "estimated_date"=> \Carbon\Carbon::today()->subDays(7)->format('Y-m-d H:i:s'),
                "expired_date"=> \Carbon\Carbon::today()->addDays(21)->format('Y-m-d H:i:s'),
                "no_quote"=> "RJO-XIII-2023-122",
                "tax"=> "12",
                "diskon"=> "0",
                "fee"=> "0",
                "nett"=> "3000000",
                "shipping"=> "50000",
                "subtotal"=> "3000000",
                "harga_total"=> "3110000",
            ],
            [
                "id_pic"=> "3",
                "id_sales"=> "2",
                "id_service"=> NULL,
                "no_pr"=> NULL,
                "title"=> "Parts of Kaiser",
                "status"=> "75",
                "status_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "po_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "upload_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "note"=> "Company Wants Nego for",
                "flag"=> "Reftech",
                "estimated_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "expired_date"=> \Carbon\Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
                "no_quote"=> "RJO-XIII-2023-125",
                "tax"=> "12",
                "diskon"=> "0",
                "fee"=> "0",
                "shipping"=> "50000",
                "subtotal"=> "1500000",
                "nett"=> "1500000",
                "harga_total"=> "1571000",
            ],
            [
                "id_pic"=> "4",
                "id_sales"=> "2",
                "id_service"=> NULL,
                "no_pr"=> NULL,
                "title"=> "Machine Atlas Copco",
                "status"=> "100",
                "status_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "po_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "upload_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "note"=> "PO Successfully",
                "estimated_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "expired_date"=> \Carbon\Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
                "no_quote"=> "RJO-XIII-2023-123",
                "tax"=> "12",
                "diskon"=> "0",
                "fee"=> "0",
                "shipping"=> "500000",
                "subtotal"=> "50000000",
                "nett"=> "50000000",
                "harga_total"=> "50600000",
            ],
            [
                "id_pic"=> "4",
                "id_sales"=> "1",
                "id_service"=> NULL,
                "no_pr"=> NULL,
                "title"=> "Machine Atlas Copco",
                "status"=> "0",
                "status_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "po_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "upload_date"=> \Carbon\Carbon::today()->format('Y-m-d H:i:s'),
                "note"=> "Price too high",
                "estimated_date"=> \Carbon\Carbon::today()->subDays(7)->format('Y-m-d H:i:s'),
                "expired_date"=> \Carbon\Carbon::today()->addDays(21)->format('Y-m-d H:i:s'),
                "no_quote"=> "RJO-XIII-2023-124",
                "tax"=> "15",
                "diskon"=> "0",
                "fee"=> "0",
                "shipping"=> "500000",
                "subtotal"=> "200000000",
                "harga_total"=> "202500000",
            ],
        ];
        Quotation::insert($quotation);
    }
}
