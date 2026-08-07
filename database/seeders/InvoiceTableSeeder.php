<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invoice = [
            [
                'id_quotation' => '1',
                'term' => 'Cash Before Delivery',
                'no_po' => '020/RJO/IV/2024',
                'no_invoice' => 'INI-INVOICE/MAU/DI/CEK-DULU-YA',
            ],
        ];
        Invoice::insert($invoice);
    }
}
