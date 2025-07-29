<?php

namespace Database\Factories\HR;

use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $firstNames = ['Ahmed', 'Mohammed', 'Abdullah', 'Omar', 'Khalid', 'Sarah', 'Fatima', 'Aisha', 'Maryam', 'Nora'];
        $lastNames = ['Al-Rashid', 'Al-Mahmoud', 'Al-Zahrani', 'Al-Otaibi', 'Al-Ghamdi', 'Al-Harbi', 'Al-Qahtani', 'Al-Dosari'];
        
        $firstNamesAr = ['أحمد', 'محمد', 'عبدالله', 'عمر', 'خالد', 'سارة', 'فاطمة', 'عائشة', 'مريم', 'نورا'];
        $lastNamesAr = ['الراشد', 'المحمود', 'الزهراني', 'العتيبي', 'الغامدي', 'الحربي', 'القحطاني', 'الدوسري'];

        $firstNameIndex = $this->faker->numberBetween(0, count($firstNames) - 1);
        $lastNameIndex = $this->faker->numberBetween(0, count($lastNames) - 1);

        return [
            'employee_number' => Employee::generateEmployeeNumber(),
            'first_name' => $firstNames[$firstNameIndex],
            'last_name' => $lastNames[$lastNameIndex],
            'first_name_ar' => $firstNamesAr[$firstNameIndex],
            'last_name_ar' => $lastNamesAr[$lastNameIndex],
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+966' . $this->faker->numerify('5########'),
            'mobile' => '+966' . $this->faker->numerify('5########'),
            'date_of_birth' => $this->faker->dateTimeBetween('-50 years', '-22 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'nationality' => 'Saudi',
            'national_id' => $this->faker->numerify('##########'),
            'passport_number' => $this->faker->optional()->regexify('[A-Z]{1}[0-9]{8}'),
            'address' => $this->faker->address,
            'address_ar' => $this->faker->address,
            'city' => $this->faker->randomElement(['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina']),
            'state' => $this->faker->randomElement(['Riyadh', 'Makkah', 'Eastern Province', 'Asir', 'Qassim']),
            'postal_code' => $this->faker->postcode,
            'country' => 'SA',
            'department_id' => Department::factory(),
            'role_id' => Role::factory(),
            'manager_id' => null,
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'probation_end_date' => function (array $attributes) {
                return $this->faker->optional()->dateTimeBetween($attributes['hire_date'], '+6 months');
            },
            'employment_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'intern']),
            'status' => 'active',
            'termination_date' => null,
            'termination_reason' => null,
            'basic_salary' => $this->faker->numberBetween(8000, 20000),
            'allowances' => [
                'housing' => $this->faker->numberBetween(2000, 5000),
                'transport' => $this->faker->numberBetween(800, 1500),
                'food' => $this->faker->numberBetween(500, 1000),
            ],
            'bank_name' => $this->faker->randomElement(['Saudi National Bank', 'Al Rajhi Bank', 'Riyad Bank', 'SABB']),
            'bank_account_number' => $this->faker->numerify('#########'),
            'iban' => 'SA' . $this->faker->numerify('######################'),
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_phone' => '+966' . $this->faker->numerify('5########'),
            'emergency_contact_relationship' => $this->faker->randomElement(['spouse', 'parent', 'sibling', 'friend']),
            'annual_leave_balance' => $this->faker->numberBetween(15, 21),
            'sick_leave_balance' => $this->faker->numberBetween(20, 30),
            'emergency_leave_balance' => $this->faker->numberBetween(3, 5),
            'notes' => $this->faker->optional()->paragraph,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terminated',
            'termination_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'termination_reason' => $this->faker->sentence,
        ]);
    }

    public function resigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resigned',
            'termination_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'termination_reason' => 'Resignation',
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'basic_salary' => $this->faker->numberBetween(18000, 25000),
        ]);
    }

    public function withManager(Employee $manager): static
    {
        return $this->state(fn (array $attributes) => [
            'manager_id' => $manager->id,
            'department_id' => $manager->department_id,
        ]);
    }
}
