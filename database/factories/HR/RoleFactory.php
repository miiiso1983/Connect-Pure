<?php

namespace Database\Factories\HR;

use App\Modules\HR\Models\Role;
use App\Modules\HR\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $jobTitles = [
            'Software Engineer', 'Project Manager', 'Business Analyst', 'Quality Assurance',
            'Marketing Specialist', 'Sales Representative', 'HR Specialist', 'Accountant',
            'Operations Manager', 'Customer Service Representative'
        ];

        $jobTitlesAr = [
            'مهندس برمجيات', 'مدير مشروع', 'محلل أعمال', 'ضمان الجودة',
            'أخصائي تسويق', 'مندوب مبيعات', 'أخصائي موارد بشرية', 'محاسب',
            'مدير عمليات', 'ممثل خدمة العملاء'
        ];

        $index = $this->faker->numberBetween(0, count($jobTitles) - 1);
        $minSalary = $this->faker->numberBetween(5000, 15000);

        return [
            'name' => $jobTitles[$index],
            'name_ar' => $jobTitlesAr[$index],
            'description' => $this->faker->paragraph,
            'description_ar' => $this->faker->paragraph,
            'code' => strtoupper($this->faker->unique()->lexify('??')),
            'department_id' => Department::factory(),
            'min_salary' => $minSalary,
            'max_salary' => $minSalary + $this->faker->numberBetween(5000, 10000),
            'level' => $this->faker->randomElement(['junior', 'mid', 'senior', 'lead', 'manager']),
            'responsibilities' => [
                $this->faker->sentence,
                $this->faker->sentence,
                $this->faker->sentence,
            ],
            'requirements' => [
                $this->faker->sentence,
                $this->faker->sentence,
                $this->faker->sentence,
            ],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'junior',
            'min_salary' => 5000,
            'max_salary' => 8000,
        ]);
    }

    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'senior',
            'min_salary' => 12000,
            'max_salary' => 18000,
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'manager',
            'min_salary' => 18000,
            'max_salary' => 25000,
        ]);
    }
}
