<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activities;

class ActivitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activities = [
            [
                'id_client' => '1',
                'name' => 'Daily Call',
                'status' => 'Responded',
                'date'        => \Carbon\Carbon::yesterday()->subDays(3)->format('Y-m-d H:i:s'),    
                'follow_up'        => \Carbon\Carbon::today()->addDays(14)->format('Y-m-d H:i:s'),
                'action' => 'Number Office',
                'note' => '-'
            ],
            [
                'id_client' => '1',
                'name' => 'Follow Up',
                'status' => 'Not Respon',
                'date'        => \Carbon\Carbon::today()->addDays(10)->format('Y-m-d H:i:s'),    
                'follow_up'        => \Carbon\Carbon::today()->addDays(24)->format('Y-m-d H:i:s'),
                'action' => 'WhatsApp',
                'note' => '-'
            ],
            [
                'id_client' => '2',
                'name' => 'Daily Call',
                'status' => 'Not Respon',
                'date'        => \Carbon\Carbon::today()->subDays(4)->format('Y-m-d H:i:s'),    
                'follow_up'        => \Carbon\Carbon::today()->addDays(10)->format('Y-m-d H:i:s'),
                'action' => 'Number Office',
                'note' => '-'
            ],
            [
                'id_client' => '3',
                'name' => 'Daily Call',
                'status' => 'Responded',
                'date'        => \Carbon\Carbon::today()->subDays(7)->format('Y-m-d H:i:s'),    
                'follow_up'        => \Carbon\Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
                'action' => 'Number Office',
                'note' => '-'
            ],
        ];
        Activities::insert($activities);
    }
}
