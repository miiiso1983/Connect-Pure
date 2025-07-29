<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\AttendanceRecord;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Models\SalaryRecord;
use App\Modules\HR\Models\PerformanceReview;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding sample data...');

        // Create sample users
        $this->createSampleUsers();
        
        // Create sample departments
        $this->createSampleDepartments();
        
        // Create sample employees
        $this->createSampleEmployees();
        
        // Create sample attendance records
        $this->createSampleAttendance();
        
        // Create sample leave requests
        $this->createSampleLeaveRequests();
        
        // Create sample salary records
        $this->createSampleSalaryRecords();
        
        // Create sample performance reviews
        $this->createSamplePerformanceReviews();

        $this->command->info('Sample data seeding completed!');
    }

    private function createSampleUsers()
    {
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr.manager@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Accounting Manager',
                'email' => 'accounting.manager@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }

    private function createSampleDepartments()
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Manages employee relations, recruitment, and HR policies',
                'budget' => 500000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Manages technology infrastructure and software development',
                'budget' => 1200000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Accounting',
                'code' => 'FIN',
                'description' => 'Manages financial operations and accounting',
                'budget' => 800000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Sales & Marketing',
                'code' => 'SAL',
                'description' => 'Drives sales growth and marketing initiatives',
                'budget' => 900000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Manages day-to-day business operations',
                'budget' => 700000.00,
                'is_active' => true,
            ],
        ];

        foreach ($departments as $deptData) {
            Department::updateOrCreate(
                ['code' => $deptData['code']],
                $deptData
            );
        }
    }

    private function createSampleEmployees()
    {
        $employees = [
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Al-Rashid',
                'email' => 'ahmed.rashid@connectpure.com',
                'employee_number' => 'EMP001',
                'department_code' => 'HR',
                'job_title' => 'HR Manager',
                'salary' => 8000.00,
                'hire_date' => now()->subMonths(18),
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Al-Zahra',
                'email' => 'fatima.zahra@connectpure.com',
                'employee_number' => 'EMP002',
                'department_code' => 'IT',
                'job_title' => 'Senior Developer',
                'salary' => 12000.00,
                'hire_date' => now()->subMonths(24),
            ],
            [
                'first_name' => 'Mohammed',
                'last_name' => 'Hassan',
                'email' => 'mohammed.hassan@connectpure.com',
                'employee_number' => 'EMP003',
                'department_code' => 'FIN',
                'job_title' => 'Financial Analyst',
                'salary' => 10000.00,
                'hire_date' => now()->subMonths(12),
            ],
            [
                'first_name' => 'Aisha',
                'last_name' => 'Ibrahim',
                'email' => 'aisha.ibrahim@connectpure.com',
                'employee_number' => 'EMP004',
                'department_code' => 'SAL',
                'job_title' => 'Sales Manager',
                'salary' => 9000.00,
                'hire_date' => now()->subMonths(15),
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Al-Mansouri',
                'email' => 'omar.mansouri@connectpure.com',
                'employee_number' => 'EMP005',
                'department_code' => 'IT',
                'job_title' => 'DevOps Engineer',
                'salary' => 11000.00,
                'hire_date' => now()->subMonths(8),
            ],
        ];

        foreach ($employees as $empData) {
            $department = Department::where('code', $empData['department_code'])->first();
            
            if ($department) {
                Employee::updateOrCreate(
                    ['employee_number' => $empData['employee_number']],
                    [
                        'first_name' => $empData['first_name'],
                        'last_name' => $empData['last_name'],
                        'email' => $empData['email'],
                        'employee_number' => $empData['employee_number'],
                        'department_id' => $department->id,
                        'job_title' => $empData['job_title'],
                        'salary' => $empData['salary'],
                        'hire_date' => $empData['hire_date'],
                        'phone' => '+971-50-' . rand(1000000, 9999999),
                        'employment_status' => 'active',
                        'date_of_birth' => now()->subYears(rand(25, 45)),
                        'address' => 'Dubai, UAE',
                        'emergency_contact_name' => 'Emergency Contact',
                        'emergency_contact_phone' => '+971-50-' . rand(1000000, 9999999),
                    ]
                );
            }
        }
    }

    private function createSampleAttendance()
    {
        $employees = Employee::all();
        $startDate = now()->subDays(30);
        
        foreach ($employees as $employee) {
            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }
                
                // 95% attendance rate
                if (rand(1, 100) <= 95) {
                    $checkIn = $date->copy()->setTime(8, rand(0, 30), 0);
                    $checkOut = $checkIn->copy()->addHours(8)->addMinutes(rand(0, 60));
                    $hoursWorked = $checkOut->diffInHours($checkIn, true);
                    
                    AttendanceRecord::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'date' => $date->toDateString(),
                        ],
                        [
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'hours_worked' => $hoursWorked,
                            'overtime_hours' => max(0, $hoursWorked - 8),
                            'status' => $checkIn->hour > 8 ? 'late' : 'present',
                            'notes' => rand(1, 10) > 8 ? 'Sample attendance note' : null,
                        ]
                    );
                }
            }
        }
    }

    private function createSampleLeaveRequests()
    {
        $employees = Employee::all();
        $leaveTypes = ['annual', 'sick', 'emergency', 'personal'];
        $statuses = ['pending', 'approved', 'rejected'];
        
        foreach ($employees as $employee) {
            // Create 2-4 leave requests per employee
            for ($i = 0; $i < rand(2, 4); $i++) {
                $startDate = now()->addDays(rand(1, 90));
                $days = rand(1, 7);
                $endDate = $startDate->copy()->addDays($days - 1);
                
                LeaveRequest::create([
                    'employee_id' => $employee->id,
                    'leave_type' => $leaveTypes[array_rand($leaveTypes)],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'days' => $days,
                    'reason' => 'Sample leave request for ' . $leaveTypes[array_rand($leaveTypes)] . ' leave',
                    'status' => $statuses[array_rand($statuses)],
                    'applied_at' => now()->subDays(rand(1, 30)),
                    'approved_by' => rand(1, 10) > 5 ? 1 : null,
                    'approved_at' => rand(1, 10) > 5 ? now()->subDays(rand(1, 15)) : null,
                ]);
            }
        }
    }

    private function createSampleSalaryRecords()
    {
        $employees = Employee::all();
        
        // Create salary records for last 6 months
        for ($month = 6; $month >= 1; $month--) {
            $periodStart = now()->subMonths($month)->startOfMonth();
            $periodEnd = $periodStart->copy()->endOfMonth();
            
            foreach ($employees as $employee) {
                $basicSalary = $employee->salary;
                $allowances = $basicSalary * 0.15; // 15% allowances
                $overtimeAmount = rand(0, 1000);
                $deductions = rand(0, 300);
                $taxDeduction = $basicSalary * 0.05; // 5% tax
                $netSalary = $basicSalary + $allowances + $overtimeAmount - $deductions - $taxDeduction;
                
                SalaryRecord::create([
                    'employee_id' => $employee->id,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'basic_salary' => $basicSalary,
                    'allowances' => $allowances,
                    'overtime_amount' => $overtimeAmount,
                    'deductions' => $deductions,
                    'tax_deduction' => $taxDeduction,
                    'net_salary' => $netSalary,
                    'working_days' => 22,
                    'worked_days' => rand(20, 22),
                    'leave_days' => rand(0, 2),
                    'overtime_hours' => rand(0, 25),
                    'status' => 'approved',
                    'prepared_by' => 1,
                    'approved_by' => 1,
                    'approved_at' => $periodEnd->addDays(5),
                ]);
            }
        }
    }

    private function createSamplePerformanceReviews()
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            // Create annual performance review
            PerformanceReview::create([
                'employee_id' => $employee->id,
                'reviewer_id' => $employees->where('id', '!=', $employee->id)->random()->id,
                'review_period' => 'annual',
                'review_date' => now()->subMonths(rand(3, 12)),
                'technical_skills' => rand(3, 5),
                'communication_skills' => rand(3, 5),
                'teamwork' => rand(3, 5),
                'leadership' => rand(2, 5),
                'problem_solving' => rand(3, 5),
                'initiative' => rand(3, 5),
                'punctuality' => rand(3, 5),
                'quality_of_work' => rand(3, 5),
                'overall_rating' => rand(3, 5),
                'achievements' => 'Demonstrated excellent performance in key areas including project delivery, team collaboration, and technical expertise.',
                'areas_for_improvement' => 'Continue developing leadership skills and explore opportunities for cross-functional collaboration.',
                'goals_next_period' => [
                    'Complete professional certification',
                    'Lead a major project initiative',
                    'Mentor junior team members',
                    'Improve specific technical skills'
                ],
                'reviewer_comments' => 'Strong performer with consistent delivery and positive attitude. Shows great potential for growth.',
                'employee_comments' => 'Thank you for the constructive feedback. I am committed to achieving the goals set for the next period.',
                'status' => 'completed',
                'completed_at' => now()->subMonths(rand(1, 6)),
                'created_by' => 1,
            ]);
        }
    }
}
