<?php

namespace Tests\Feature\HR;

use App\Models\User;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected $department;

    protected $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->department = Department::factory()->create();
        $this->role = Role::factory()->create(['department_id' => $this->department->id]);
    }

    /** @test */
    public function user_can_view_employee_index()
    {
        Employee::factory()->count(3)->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.employees.index'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.hr.employees.index');
        $response->assertViewHas('employees');
    }

    /** @test */
    public function user_can_create_employee()
    {
        Storage::fake('public');

        $employeeData = [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashid',
            'first_name_ar' => 'أحمد',
            'last_name_ar' => 'الراشد',
            'email' => 'ahmed@example.com',
            'phone' => '+966501234567',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'marital_status' => 'married',
            'nationality' => 'Saudi',
            'national_id' => '1234567890',
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
            'hire_date' => now()->format('Y-m-d'),
            'employment_type' => 'full_time',
            'basic_salary' => 10000,
            'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
        ];

        $response = $this->actingAs($this->user)
            ->post(route('modules.hr.employees.store'), $employeeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('hr_employees', [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashid',
            'email' => 'ahmed@example.com',
        ]);

        $employee = Employee::where('email', 'ahmed@example.com')->first();
        $this->assertNotNull($employee->employee_number);
        $this->assertTrue(str_starts_with($employee->employee_number, 'EMP'));
    }

    /** @test */
    public function employee_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('modules.hr.employees.store'), []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'department_id',
            'role_id',
            'hire_date',
            'employment_type',
            'basic_salary',
        ]);
    }

    /** @test */
    public function employee_creation_validates_unique_email()
    {
        Employee::factory()->create(['email' => 'test@example.com']);

        $employeeData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
            'hire_date' => now()->format('Y-m-d'),
            'employment_type' => 'full_time',
            'basic_salary' => 10000,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('modules.hr.employees.store'), $employeeData);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_can_view_employee_details()
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.employees.show', $employee));

        $response->assertStatus(200);
        $response->assertViewIs('modules.hr.employees.show');
        $response->assertViewHas('employee');
    }

    /** @test */
    public function user_can_update_employee()
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $updateData = [
            'first_name' => 'Updated Name',
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
            'hire_date' => $employee->hire_date->format('Y-m-d'),
            'employment_type' => $employee->employment_type,
            'status' => 'active',
            'basic_salary' => 15000,
            'annual_leave_balance' => 21,
            'sick_leave_balance' => 30,
            'emergency_leave_balance' => 5,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('modules.hr.employees.update', $employee), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('hr_employees', [
            'id' => $employee->id,
            'first_name' => 'Updated Name',
            'basic_salary' => 15000,
        ]);
    }

    /** @test */
    public function user_can_delete_employee_without_dependencies()
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('modules.hr.employees.destroy', $employee));

        $response->assertRedirect();
        $this->assertDatabaseMissing('hr_employees', ['id' => $employee->id]);
    }

    /** @test */
    public function user_cannot_delete_employee_with_subordinates()
    {
        $manager = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $subordinate = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
            'manager_id' => $manager->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('modules.hr.employees.destroy', $manager));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('hr_employees', ['id' => $manager->id]);
    }

    /** @test */
    public function user_can_filter_employees_by_department()
    {
        $otherDepartment = Department::factory()->create();
        $otherRole = Role::factory()->create(['department_id' => $otherDepartment->id]);

        Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        Employee::factory()->create([
            'department_id' => $otherDepartment->id,
            'role_id' => $otherRole->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.employees.index', ['department_id' => $this->department->id]));

        $response->assertStatus(200);
        $employees = $response->viewData('employees');
        $this->assertEquals(1, $employees->count());
    }

    /** @test */
    public function user_can_search_employees()
    {
        Employee::factory()->create([
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashid',
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        Employee::factory()->create([
            'first_name' => 'Sarah',
            'last_name' => 'Al-Mahmoud',
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.employees.index', ['search' => 'Ahmed']));

        $response->assertStatus(200);
        $employees = $response->viewData('employees');
        $this->assertEquals(1, $employees->count());
    }

    /** @test */
    public function user_can_export_employees()
    {
        Employee::factory()->count(3)->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.employees.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function employee_number_is_auto_generated()
    {
        $employee1 = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $employee2 = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $this->assertNotNull($employee1->employee_number);
        $this->assertNotNull($employee2->employee_number);
        $this->assertNotEquals($employee1->employee_number, $employee2->employee_number);
        $this->assertTrue(str_starts_with($employee1->employee_number, 'EMP'));
        $this->assertTrue(str_starts_with($employee2->employee_number, 'EMP'));
    }

    /** @test */
    public function employee_can_have_manager_relationship()
    {
        $manager = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $employee = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
            'manager_id' => $manager->id,
        ]);

        $this->assertEquals($manager->id, $employee->manager->id);
        $this->assertTrue($manager->subordinates->contains($employee));
    }
}
