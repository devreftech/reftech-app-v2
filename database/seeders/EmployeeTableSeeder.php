<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds. Assumes DepartmentTableSeeder/PositionTableSeeder
     * have run first. Creates dummy employees not tied to any real `users`
     * row, so this is safe to run on top of production-like data without
     * colliding with the real hr:backfill-employees command.
     *
     * @return void
     */
    public function run()
    {
        $departments = Department::all();
        $positions = Position::all();

        if ($departments->isEmpty() || $positions->isEmpty()) {
            $this->call([
                DepartmentTableSeeder::class,
                PositionTableSeeder::class,
            ]);
            $departments = Department::all();
            $positions = Position::all();
        }

        // Override id_department/id_position per-instance so the factory doesn't
        // spawn its own throwaway Department/Position rows for each employee.
        Employee::factory()
            ->count(20)
            ->state(fn () => [
                'id_department' => $departments->random()->id,
                'id_position' => $positions->random()->id,
            ])
            ->create();
    }
}
