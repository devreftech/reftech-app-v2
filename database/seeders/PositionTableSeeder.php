<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionTableSeeder extends Seeder
{
    /**
     * Run the database seeds. Assumes DepartmentTableSeeder has run first.
     *
     * @return void
     */
    public function run()
    {
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $departments = Department::factory()->count(3)->create();
        }

        foreach ($departments as $department) {
            Position::factory()
                ->count(3)
                ->create(['id_department' => $department->id]);
        }
    }
}
