<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    public function definition()
    {
        return [
            'id_department' => Department::factory(),
            'name' => fake()->jobTitle(),
            'level' => fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
