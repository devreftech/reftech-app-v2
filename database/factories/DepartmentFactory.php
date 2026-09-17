<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Sales & Marketing', 'Finance', 'Accounting', 'Logistic & Warehouse',
                'Technical Service', 'Human Resources', 'IT & Development', 'Operations',
            ]),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'parent_id' => null,
            'is_active' => true,
        ];
    }
}
