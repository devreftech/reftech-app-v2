<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MachineTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {
            $machine = [
                [
                    'id_client' => '1',
                    'brand' => 'Kaeser',
                    'type' => 'BSD',
                    'serial_number' => 'BSD 70',
                    'bar' => '8',
                    'running' => '3600',
                ],
                [
                    'id_client' => '1',
                    'brand' => 'AIRN',
                    'type' => 'D',
                    'serial_number' => 'D 75-8',
                    'bar' => '8',
                    'running' => '3600',
                ],
                [
                    'id_client' => '2',
                    'brand' => 'Kaeser',
                    'type' => 'CSD',
                    'serial_number' => 'CSD 102',
                    'bar' => '11',
                    'running' => '3600',
                ],
                [
                    'id_client' => '3',
                    'brand' => 'AIRN',
                    'type' => 'REF',
                    'serial_number' => 'REF-70',
                    'bar' => '8',
                    'running' => '3600',
                ],
            ];
            Machine::insert($machine);
        }
    }
}
