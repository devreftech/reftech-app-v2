<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pic;

class PICTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pic = [
            [
                'id_client' => '1',
                'name_pic' => 'Asep Saepullah',
                'position' => 'Lead Marketing',
                'email_pic' => 'asepsaep@gmail.com',
                'phone_pic' => '+628123456789',
            ],
            [
                'id_client' => '1',
                'name_pic' => 'Gunawan',
                'position' => 'Manager Pruchasing',
                'email_pic' => 'gunawan@gmail.com',
                'phone_pic' => '+628126478789',
            ],
            [
                'id_client' => '2',
                'name_pic' => 'Hakim Kamilin',
                'position' => 'Sales',
                'email_pic' => 'Hakimkam@gmail.com',
                'phone_pic' => '+62894651325',
            ],
            [
                'id_client' => '3',
                'name_pic' => 'Budi Gunawan',
                'position' => 'CEO',
                'email_pic' => 'pabudinih@gmail.com',
                'phone_pic' => '+628215498753',
            ],
        ];
        Pic::insert($pic);
    }
}
