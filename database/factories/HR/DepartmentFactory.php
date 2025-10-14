<?php

namespace Database\Factories\HR;

use App\Modules\HR\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company.' Department',
            'name_ar' => 'قسم '.$this->faker->company,
            'description' => $this->faker->paragraph,
            'description_ar' => $this->faker->paragraph,
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'budget' => $this->faker->numberBetween(100000, 1000000),
            'location' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
