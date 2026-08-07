<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Issues;

class IssuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $issues = [
            [
                'issue' => 'New Client'
            ],
            [
                'issue' => 'Not Respond'
            ],
            [
                'issue' => 'Send Introduction'
            ],
            [
                'issue' => 'Send Quote'
            ],
            [
                'issue' => 'Done PO'
            ],
            [
                'issue' => 'Loss'
            ],
        ];
        Issues::insert($issues);
    }
}
