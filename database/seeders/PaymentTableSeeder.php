<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment = [
            [
                'id_payment' => '1',
                'file' => 'asset/payment/INI-INVOICE/MAU/DI/CEK-DULU-YA',
                'amount' => '6000000',
                'note' => 'DP 50% & Cash 50%',
            ],
        ];
        Payment::insert($payment);
    }
}
