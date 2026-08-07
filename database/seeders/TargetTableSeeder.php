<?php

namespace Database\Seeders;

use App\Models\Target;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TargetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $target = [
            [
                'id_sales' => '1',
                'dc' => '600',
                'statDc' => '1',
                'crm' => '50',
                'statCrm' => '1',
                'visit' => '40',
                'statVisit' => '1',
                'quote' => '100',
                'statQuote' => '1',
                'po' => '20',
                'statPo' => '1',
                'total' => '150000000',
            ],
            [
                'id_sales' => '2',
                'dc' => '600',
                'statDc' => '1',
                'crm' => '50',
                'statCrm' => '1',
                'visit' => '40',
                'statVisit' => '1',
                'quote' => '100',
                'statQuote' => '1',
                'po' => '20',
                'statPo' => '1',
                'total' => '150000000',
            ],
            [
                'id_sales' => '3',
                'dc' => '600',
                'statDc' => '1',
                'crm' => '50',
                'statCrm' => '1',
                'visit' => '40',
                'statVisit' => '1',
                'quote' => '100',
                'statQuote' => '1',
                'po' => '20',
                'statPo' => '1',
                'total' => '150000000',
            ],
            [
                'id_sales' => '4',
                'dc' => '600',
                'statDc' => '1',
                'crm' => '50',
                'statCrm' => '1',
                'visit' => '40',
                'statVisit' => '1',
                'quote' => '100',
                'statQuote' => '1',
                'po' => '20',
                'statPo' => '1',
                'total' => '150000000',
            ],
            [
                'id_sales' => '14',
                'dc' => '600',
                'statDc' => '1',
                'crm' => '50',
                'statCrm' => '1',
                'visit' => '40',
                'statVisit' => '1',
                'quote' => '100',
                'statQuote' => '1',
                'po' => '20',
                'statPo' => '1',
                'total' => '150000000',
            ],
            [
                'id_sales' => '15',
                'dc' => '600',
                'statDc' => '1',
                'crm' => '50',
                'statCrm' => '1',
                'visit' => '40',
                'statVisit' => '1',
                'quote' => '100',
                'statQuote' => '1',
                'po' => '20',
                'statPo' => '1',
                'total' => '150000000',
            ],
            [
                'id_sales' => '16',
                'dc' => '600',
                'statDc' => '1',
                'crm' => '50',
                'statCrm' => '1',
                'visit' => '40',
                'statVisit' => '1',
                'quote' => '100',
                'statQuote' => '1',
                'po' => '20',
                'statPo' => '1',
                'total' => '150000000',
            ],
        ];
        Target::insert($target);
    }
}
