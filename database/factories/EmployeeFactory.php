<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    public function definition()
    {
        $status = fake()->randomElement(['Tetap', 'Kontrak', 'Probation']);

        return [
            // Dummy employee tidak ditautkan ke user manapun secara default, supaya
            // aman dijalankan berulang tanpa bentrok dengan unique index user_id.
            // Pakai state forUser() kalau butuh employee yang tertaut ke user tertentu.
            'user_id' => null,
            'id_department' => Department::factory(),
            'id_position' => Position::factory(),
            'nik' => fake()->unique()->numerify('NIK#########'),
            'join_date' => fake()->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
            'birthday' => fake()->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'employment_status' => $status,
            'contract_start_date' => $status === 'Kontrak' ? fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d') : null,
            'contract_end_date' => $status === 'Kontrak' ? fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d') : null,
            'resign_date' => null,
        ];
    }

    public function forUser(int $userId)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}
