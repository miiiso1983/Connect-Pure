<?php

namespace Database\Seeders;

use App\Modules\HR\Models\Attendance;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Models\Role;
use App\Modules\HR\Models\SalaryRecord;
use Illuminate\Database\Seeder;

class HRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if no data exists
        if (Department::count() === 0) {
            $this->createDepartments();
        }

        if (Role::count() === 0) {
            $this->createRoles();
        }

        if (Employee::count() === 0) {
            $this->createEmployees();
        }

        if (LeaveRequest::count() === 0) {
            $this->createLeaveRequests();
        }

        if (Attendance::count() === 0) {
            $this->createAttendanceRecords();
        }

        if (SalaryRecord::count() === 0) {
            $this->createSalaryRecords();
        }
    }

    /**
     * Create departments.
     */
    private function createDepartments(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'name_ar' => 'تقنية المعلومات',
                'description' => 'Responsible for all technology infrastructure and software development',
                'description_ar' => 'مسؤول عن جميع البنية التحتية التقنية وتطوير البرمجيات',
                'code' => 'IT',
                'budget' => 500000,
                'location' => 'Building A, Floor 3',
                'phone' => '+966-11-123-4567',
                'email' => 'it@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'name_ar' => 'الموارد البشرية',
                'description' => 'Manages employee relations, recruitment, and HR policies',
                'description_ar' => 'إدارة علاقات الموظفين والتوظيف وسياسات الموارد البشرية',
                'code' => 'HR',
                'budget' => 300000,
                'location' => 'Building A, Floor 2',
                'phone' => '+966-11-123-4568',
                'email' => 'hr@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Accounting',
                'name_ar' => 'المالية والمحاسبة',
                'description' => 'Handles financial planning, accounting, and budget management',
                'description_ar' => 'التخطيط المالي والمحاسبة وإدارة الميزانية',
                'code' => 'FIN',
                'budget' => 400000,
                'location' => 'Building A, Floor 1',
                'phone' => '+966-11-123-4569',
                'email' => 'finance@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Sales & Marketing',
                'name_ar' => 'المبيعات والتسويق',
                'description' => 'Drives sales growth and marketing initiatives',
                'description_ar' => 'قيادة نمو المبيعات ومبادرات التسويق',
                'code' => 'SAL',
                'budget' => 600000,
                'location' => 'Building B, Floor 1',
                'phone' => '+966-11-123-4570',
                'email' => 'sales@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'name_ar' => 'العمليات',
                'description' => 'Manages day-to-day operations and logistics',
                'description_ar' => 'إدارة العمليات اليومية واللوجستيات',
                'code' => 'OPS',
                'budget' => 350000,
                'location' => 'Building B, Floor 2',
                'phone' => '+966-11-123-4571',
                'email' => 'operations@company.com',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }

    /**
     * Create roles.
     */
    private function createRoles(): void
    {
        $roles = [
            // IT Department
            [
                'name' => 'Software Engineer',
                'name_ar' => 'مهندس برمجيات',
                'description' => 'Develops and maintains software applications',
                'code' => 'SE',
                'department_id' => 1,
                'min_salary' => 8000,
                'max_salary' => 15000,
                'level' => 'mid',
                'responsibilities' => ['Code development', 'Testing', 'Documentation'],
                'requirements' => ['Bachelor in CS', '3+ years experience', 'Programming skills'],
                'is_active' => true,
            ],
            [
                'name' => 'IT Manager',
                'name_ar' => 'مدير تقنية المعلومات',
                'description' => 'Manages IT department and technology strategy',
                'code' => 'ITM',
                'department_id' => 1,
                'min_salary' => 18000,
                'max_salary' => 25000,
                'level' => 'manager',
                'responsibilities' => ['Team management', 'Strategic planning', 'Budget oversight'],
                'requirements' => ['Bachelor in CS/IT', '8+ years experience', 'Leadership skills'],
                'is_active' => true,
            ],
            // HR Department
            [
                'name' => 'HR Specialist',
                'name_ar' => 'أخصائي موارد بشرية',
                'description' => 'Handles recruitment and employee relations',
                'code' => 'HRS',
                'department_id' => 2,
                'min_salary' => 6000,
                'max_salary' => 10000,
                'level' => 'mid',
                'responsibilities' => ['Recruitment', 'Employee relations', 'Policy implementation'],
                'requirements' => ['Bachelor in HR', '2+ years experience', 'Communication skills'],
                'is_active' => true,
            ],
            [
                'name' => 'HR Manager',
                'name_ar' => 'مدير الموارد البشرية',
                'description' => 'Leads HR department and strategic initiatives',
                'code' => 'HRM',
                'department_id' => 2,
                'min_salary' => 15000,
                'max_salary' => 20000,
                'level' => 'manager',
                'responsibilities' => ['HR strategy', 'Team leadership', 'Policy development'],
                'requirements' => ['Bachelor in HR', '6+ years experience', 'Strategic thinking'],
                'is_active' => true,
            ],
            // Finance Department
            [
                'name' => 'Accountant',
                'name_ar' => 'محاسب',
                'description' => 'Manages financial records and transactions',
                'code' => 'ACC',
                'department_id' => 3,
                'min_salary' => 5000,
                'max_salary' => 9000,
                'level' => 'mid',
                'responsibilities' => ['Financial recording', 'Report preparation', 'Compliance'],
                'requirements' => ['Bachelor in Accounting', '2+ years experience', 'Attention to detail'],
                'is_active' => true,
            ],
            [
                'name' => 'Finance Manager',
                'name_ar' => 'مدير المالية',
                'description' => 'Oversees financial operations and planning',
                'code' => 'FM',
                'department_id' => 3,
                'min_salary' => 16000,
                'max_salary' => 22000,
                'level' => 'manager',
                'responsibilities' => ['Financial planning', 'Budget management', 'Team oversight'],
                'requirements' => ['Bachelor in Finance', '7+ years experience', 'Analytical skills'],
                'is_active' => true,
            ],
            // Sales Department
            [
                'name' => 'Sales Representative',
                'name_ar' => 'مندوب مبيعات',
                'description' => 'Drives sales and customer relationships',
                'code' => 'SR',
                'department_id' => 4,
                'min_salary' => 4000,
                'max_salary' => 8000,
                'level' => 'junior',
                'responsibilities' => ['Customer outreach', 'Sales presentations', 'Relationship building'],
                'requirements' => ['High school diploma', '1+ years experience', 'Communication skills'],
                'is_active' => true,
            ],
            [
                'name' => 'Sales Manager',
                'name_ar' => 'مدير المبيعات',
                'description' => 'Leads sales team and strategy',
                'code' => 'SM',
                'department_id' => 4,
                'min_salary' => 14000,
                'max_salary' => 20000,
                'level' => 'manager',
                'responsibilities' => ['Sales strategy', 'Team management', 'Target achievement'],
                'requirements' => ['Bachelor degree', '5+ years experience', 'Leadership skills'],
                'is_active' => true,
            ],
            // Operations Department
            [
                'name' => 'Operations Coordinator',
                'name_ar' => 'منسق العمليات',
                'description' => 'Coordinates daily operations and logistics',
                'code' => 'OC',
                'department_id' => 5,
                'min_salary' => 5500,
                'max_salary' => 9500,
                'level' => 'mid',
                'responsibilities' => ['Operations coordination', 'Process improvement', 'Quality control'],
                'requirements' => ['Bachelor degree', '2+ years experience', 'Organizational skills'],
                'is_active' => true,
            ],
            [
                'name' => 'Operations Manager',
                'name_ar' => 'مدير العمليات',
                'description' => 'Manages operations department and processes',
                'code' => 'OM',
                'department_id' => 5,
                'min_salary' => 15000,
                'max_salary' => 21000,
                'level' => 'manager',
                'responsibilities' => ['Operations strategy', 'Process optimization', 'Team leadership'],
                'requirements' => ['Bachelor degree', '6+ years experience', 'Process management'],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }

    /**
     * Create employees.
     */
    private function createEmployees(): void
    {
        $employees = [
            [
                'employee_number' => 'EMP1001',
                'first_name' => 'Ahmed',
                'last_name' => 'Al-Rashid',
                'first_name_ar' => 'أحمد',
                'last_name_ar' => 'الراشد',
                'email' => 'ahmed.alrashid@company.com',
                'phone' => '+966-50-123-4567',
                'mobile' => '+966-50-123-4567',
                'date_of_birth' => '1985-03-15',
                'gender' => 'male',
                'marital_status' => 'married',
                'nationality' => 'Saudi',
                'national_id' => '1234567890',
                'address' => 'Riyadh, King Fahd District',
                'address_ar' => 'الرياض، حي الملك فهد',
                'city' => 'Riyadh',
                'country' => 'SA',
                'department_id' => 1,
                'role_id' => 2, // IT Manager
                'hire_date' => '2020-01-15',
                'employment_type' => 'full_time',
                'status' => 'active',
                'basic_salary' => 20000,
                'allowances' => ['housing' => 5000, 'transport' => 1500],
                'bank_name' => 'Saudi National Bank',
                'bank_account_number' => '123456789',
                'iban' => 'SA1234567890123456789',
                'emergency_contact_name' => 'Fatima Al-Rashid',
                'emergency_contact_phone' => '+966-50-987-6543',
                'emergency_contact_relationship' => 'Wife',
                'annual_leave_balance' => 21,
                'sick_leave_balance' => 30,
                'emergency_leave_balance' => 5,
            ],
            [
                'employee_number' => 'EMP1002',
                'first_name' => 'Sarah',
                'last_name' => 'Al-Mahmoud',
                'first_name_ar' => 'سارة',
                'last_name_ar' => 'المحمود',
                'email' => 'sarah.almahmoud@company.com',
                'phone' => '+966-50-234-5678',
                'mobile' => '+966-50-234-5678',
                'date_of_birth' => '1990-07-22',
                'gender' => 'female',
                'marital_status' => 'single',
                'nationality' => 'Saudi',
                'national_id' => '2345678901',
                'address' => 'Riyadh, Olaya District',
                'address_ar' => 'الرياض، حي العليا',
                'city' => 'Riyadh',
                'country' => 'SA',
                'department_id' => 2,
                'role_id' => 4, // HR Manager
                'manager_id' => 1,
                'hire_date' => '2021-03-01',
                'employment_type' => 'full_time',
                'status' => 'active',
                'basic_salary' => 17000,
                'allowances' => ['housing' => 4000, 'transport' => 1200],
                'bank_name' => 'Al Rajhi Bank',
                'bank_account_number' => '234567890',
                'iban' => 'SA2345678901234567890',
                'emergency_contact_name' => 'Mohammed Al-Mahmoud',
                'emergency_contact_phone' => '+966-50-876-5432',
                'emergency_contact_relationship' => 'Father',
                'annual_leave_balance' => 18,
                'sick_leave_balance' => 25,
                'emergency_leave_balance' => 3,
            ],
            [
                'employee_number' => 'EMP1003',
                'first_name' => 'Omar',
                'last_name' => 'Al-Zahrani',
                'first_name_ar' => 'عمر',
                'last_name_ar' => 'الزهراني',
                'email' => 'omar.alzahrani@company.com',
                'phone' => '+966-50-345-6789',
                'mobile' => '+966-50-345-6789',
                'date_of_birth' => '1988-11-10',
                'gender' => 'male',
                'marital_status' => 'married',
                'nationality' => 'Saudi',
                'national_id' => '3456789012',
                'address' => 'Riyadh, Diplomatic Quarter',
                'address_ar' => 'الرياض، الحي الدبلوماسي',
                'city' => 'Riyadh',
                'country' => 'SA',
                'department_id' => 1,
                'role_id' => 1, // Software Engineer
                'manager_id' => 1,
                'hire_date' => '2021-06-15',
                'employment_type' => 'full_time',
                'status' => 'active',
                'basic_salary' => 12000,
                'allowances' => ['housing' => 3000, 'transport' => 1000],
                'bank_name' => 'Riyad Bank',
                'bank_account_number' => '345678901',
                'iban' => 'SA3456789012345678901',
                'emergency_contact_name' => 'Aisha Al-Zahrani',
                'emergency_contact_phone' => '+966-50-765-4321',
                'emergency_contact_relationship' => 'Wife',
                'annual_leave_balance' => 15,
                'sick_leave_balance' => 20,
                'emergency_leave_balance' => 2,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        // Update department managers
        Department::where('id', 1)->update(['manager_id' => 1]); // Ahmed as IT Manager
        Department::where('id', 2)->update(['manager_id' => 2]); // Sarah as HR Manager
    }

    /**
     * Create leave requests.
     */
    private function createLeaveRequests(): void
    {
        $leaveRequests = [
            [
                'request_number' => 'LR1001',
                'employee_id' => 2,
                'leave_type' => 'annual',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(14),
                'total_days' => 5,
                'reason' => 'Family vacation',
                'reason_ar' => 'إجازة عائلية',
                'status' => 'pending',
            ],
            [
                'request_number' => 'LR1002',
                'employee_id' => 3,
                'leave_type' => 'sick',
                'start_date' => now()->subDays(3),
                'end_date' => now()->subDays(1),
                'total_days' => 3,
                'reason' => 'Medical treatment',
                'reason_ar' => 'علاج طبي',
                'status' => 'approved',
                'approver_id' => 1,
                'approved_at' => now()->subDays(4),
            ],
        ];

        foreach ($leaveRequests as $request) {
            LeaveRequest::create($request);
        }
    }

    /**
     * Create attendance records.
     */
    private function createAttendanceRecords(): void
    {
        $employees = Employee::all();

        // Create attendance for the last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);

            // Skip weekends (Friday and Saturday)
            if (in_array($date->dayOfWeek, [5, 6])) {
                continue;
            }

            foreach ($employees as $employee) {
                $status = 'present';
                $actualIn = $date->copy()->setTime(9, rand(0, 30)); // 9:00-9:30 AM
                $actualOut = $date->copy()->setTime(17, rand(0, 30)); // 5:00-5:30 PM

                // Randomly make some employees late or absent
                $random = rand(1, 100);
                if ($random <= 5) { // 5% absent
                    $status = 'absent';
                    $actualIn = null;
                    $actualOut = null;
                } elseif ($random <= 15) { // 10% late
                    $status = 'late';
                    $actualIn = $date->copy()->setTime(9, rand(31, 59)); // Late arrival
                }

                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'scheduled_in' => '09:00:00',
                    'scheduled_out' => '17:00:00',
                    'actual_in' => $actualIn,
                    'actual_out' => $actualOut,
                    'status' => $status,
                    'location' => 'Office',
                    'is_approved' => true,
                    'approved_by' => 1,
                    'approved_at' => $date->copy()->addHours(8),
                ]);

                if ($actualIn && $actualOut) {
                    $attendance->calculateWorkingHours();
                }
            }
        }
    }

    /**
     * Create salary records.
     */
    private function createSalaryRecords(): void
    {
        $employees = Employee::all();
        $currentMonth = now();
        $lastMonth = now()->subMonth();

        foreach ([$lastMonth, $currentMonth] as $month) {
            foreach ($employees as $employee) {
                $housingAllowance = $employee->allowances['housing'] ?? 0;
                $transportAllowance = $employee->allowances['transport'] ?? 0;
                $socialInsurance = $employee->basic_salary * 0.09;
                $incomeTax = $employee->basic_salary * 0.05;

                $grossSalary = $employee->basic_salary + $housingAllowance + $transportAllowance;
                $totalDeductions = $socialInsurance + $incomeTax;
                $netSalary = $grossSalary - $totalDeductions;

                SalaryRecord::create([
                    'payroll_number' => SalaryRecord::generatePayrollNumber(),
                    'employee_id' => $employee->id,
                    'period_start' => $month->copy()->startOfMonth(),
                    'period_end' => $month->copy()->endOfMonth(),
                    'year' => $month->year,
                    'month' => $month->month,
                    'working_days' => $month->copy()->endOfMonth()->day,
                    'actual_working_days' => $month->copy()->endOfMonth()->day - 2, // Assume 2 days off
                    'basic_salary' => $employee->basic_salary,
                    'housing_allowance' => $housingAllowance,
                    'transport_allowance' => $transportAllowance,
                    'social_insurance' => $socialInsurance,
                    'income_tax' => $incomeTax,
                    'gross_salary' => $grossSalary,
                    'total_deductions' => $totalDeductions,
                    'net_salary' => $netSalary,
                    'status' => $month->isSameMonth(now()) ? 'draft' : 'paid',
                    'prepared_by' => 2, // HR Manager
                    'approved_by' => $month->isSameMonth(now()) ? null : 1,
                    'approved_at' => $month->isSameMonth(now()) ? null : $month->copy()->endOfMonth(),
                    'payment_date' => $month->isSameMonth(now()) ? null : $month->copy()->endOfMonth()->addDays(5),
                    'payment_method' => $month->isSameMonth(now()) ? null : 'bank_transfer',
                ]);
            }
        }

        // Salary records already have calculated totals
    }
}
