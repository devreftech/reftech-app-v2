<?php

namespace Database\Seeders;

use App\Models\DetailUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $detail = [
            [
                'id_users' => '1',
                'position' => 'Sales',
                'roles' => 'Sales',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '2',
                'position' => 'Sales',
                'roles' => 'Sales',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '3',
                'position' => 'Sales',
                'roles' => 'Sales',
                'area' => 'Jabodetabek',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '4',
                'position' => 'Sales',
                'roles' => 'Sales',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '5',
                'position' => 'Admin',
                'roles' => 'Admin',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '6',
                'position' => 'Admin',
                'roles' => 'Admin',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '7',
                'position' => 'Admin',
                'roles' => 'Admin',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '8',
                'position' => 'Technician',
                'roles' => 'Technician',
                'area' => 'Bekasi',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '9',
                'position' => 'Technician',
                'roles' => 'Technician',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '10',
                'position' => 'Technician',
                'roles' => 'Technician',
                'area' => 'Bekasi',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '11',
                'position' => 'Technician',
                'roles' => 'Technician',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '12',
                'position' => 'Technician',
                'roles' => 'Technician',
                'area' => 'Bekasi',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '13',
                'position' => 'Sales',
                'roles' => 'Sales',
                'area' => 'Bekasi',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '14',
                'position' => 'Sales',
                'roles' => 'Sales',
                'area' => 'Jawa Barat',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '15',
                'position' => 'Admin',
                'roles' => 'Admin',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '16',
                'position' => 'Sales',
                'roles' => 'Sales',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '17',
                'position' => 'Admin',
                'roles' => 'Admin',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
            [
                'id_users' => '18',
                'position' => 'Logistic',
                'roles' => 'Logistic',
                'area' => 'Bandung',
                'date' => \Carbon\Carbon::today(),
            ],
        ];
        DetailUser::insert($detail);
    }
}
