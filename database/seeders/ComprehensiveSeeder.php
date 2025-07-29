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
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Vendor;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Currency;
use App\Modules\Accounting\Models\Tax;
use Illuminate\Support\Facades\Hash;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive data seeding...');

        // Seed Users
        $this->seedUsers();
        
        // Seed HR Data
        $this->seedHRData();
        
        // Seed Accounting Data
        $this->seedAccountingData();
        
        $this->command->info('Comprehensive data seeding completed!');
    }

    private function seedUsers()
    {
        $this->command->info('Seeding users...');

        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@connectpure.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // HR Manager
        User::updateOrCreate(
            ['email' => 'hr@connectpure.com'],
            [
                'name' => 'HR Manager',
                'email' => 'hr@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Accounting Manager
        User::updateOrCreate(
            ['email' => 'accounting@connectpure.com'],
            [
                'name' => 'Accounting Manager',
                'email' => 'accounting@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedHRData()
    {
        $this->command->info('Seeding HR data...');

        // Seed Departments
        $departments = [
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Human Resources Department', 'budget' => 500000],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'IT Department', 'budget' => 1000000],
            ['name' => 'Finance & Accounting', 'code' => 'FIN', 'description' => 'Finance and Accounting Department', 'budget' => 750000],
            ['name' => 'Sales & Marketing', 'code' => 'SAL', 'description' => 'Sales and Marketing Department', 'budget' => 800000],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Operations Department', 'budget' => 600000],
            ['name' => 'Customer Support', 'code' => 'SUP', 'description' => 'Customer Support Department', 'budget' => 400000],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }

        // Seed Employees
        $employees = [
            ['first_name' => 'Ahmed', 'last_name' => 'Al-Rashid', 'email' => 'ahmed.rashid@connectpure.com', 'employee_number' => 'EMP001', 'department_code' => 'HR', 'salary' => 8000, 'job_title' => 'HR Manager'],
            ['first_name' => 'Fatima', 'last_name' => 'Al-Zahra', 'email' => 'fatima.zahra@connectpure.com', 'employee_number' => 'EMP002', 'department_code' => 'IT', 'salary' => 12000, 'job_title' => 'Senior Developer'],
            ['first_name' => 'Mohammed', 'last_name' => 'Hassan', 'email' => 'mohammed.hassan@connectpure.com', 'employee_number' => 'EMP003', 'department_code' => 'FIN', 'salary' => 10000, 'job_title' => 'Financial Analyst'],
            ['first_name' => 'Aisha', 'last_name' => 'Ibrahim', 'email' => 'aisha.ibrahim@connectpure.com', 'employee_number' => 'EMP004', 'department_code' => 'SAL', 'salary' => 9000, 'job_title' => 'Sales Manager'],
            ['first_name' => 'Omar', 'last_name' => 'Al-Mansouri', 'email' => 'omar.mansouri@connectpure.com', 'employee_number' => 'EMP005', 'department_code' => 'IT', 'salary' => 11000, 'job_title' => 'DevOps Engineer'],
            ['first_name' => 'Layla', 'last_name' => 'Al-Qasimi', 'email' => 'layla.qasimi@connectpure.com', 'employee_number' => 'EMP006', 'department_code' => 'OPS', 'salary' => 7500, 'job_title' => 'Operations Coordinator'],
            ['first_name' => 'Khalid', 'last_name' => 'Al-Maktoum', 'email' => 'khalid.maktoum@connectpure.com', 'employee_number' => 'EMP007', 'department_code' => 'SUP', 'salary' => 6500, 'job_title' => 'Support Specialist'],
            ['first_name' => 'Nour', 'last_name' => 'Al-Sabah', 'email' => 'nour.sabah@connectpure.com', 'employee_number' => 'EMP008', 'department_code' => 'IT', 'salary' => 9500, 'job_title' => 'Frontend Developer'],
            ['first_name' => 'Yusuf', 'last_name' => 'Al-Thani', 'email' => 'yusuf.thani@connectpure.com', 'employee_number' => 'EMP009', 'department_code' => 'SAL', 'salary' => 8500, 'job_title' => 'Marketing Specialist'],
            ['first_name' => 'Maryam', 'last_name' => 'Al-Nahyan', 'email' => 'maryam.nahyan@connectpure.com', 'employee_number' => 'EMP010', 'department_code' => 'FIN', 'salary' => 9200, 'job_title' => 'Accountant'],
        ];

        foreach ($employees as $emp) {
            $department = Department::where('code', $emp['department_code'])->first();
            Employee::updateOrCreate(
                ['employee_number' => $emp['employee_number']],
                [
                    'first_name' => $emp['first_name'],
                    'last_name' => $emp['last_name'],
                    'email' => $emp['email'],
                    'employee_number' => $emp['employee_number'],
                    'department_id' => $department->id,
                    'salary' => $emp['salary'],
                    'job_title' => $emp['job_title'],
                    'hire_date' => now()->subMonths(rand(1, 24)),
                    'phone' => '+971-50-' . rand(1000000, 9999999),
                    'employment_status' => 'active',
                ]
            );
        }

        // Seed Attendance Records
        $this->seedAttendanceRecords();
        
        // Seed Leave Requests
        $this->seedLeaveRequests();
        
        // Seed Salary Records
        $this->seedSalaryRecords();
        
        // Seed Performance Reviews
        $this->seedPerformanceReviews();
    }

    private function seedAttendanceRecords()
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
                
                // 90% attendance rate
                if (rand(1, 100) <= 90) {
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
                        ]
                    );
                }
            }
        }
    }

    private function seedLeaveRequests()
    {
        $employees = Employee::all();
        $leaveTypes = ['annual', 'sick', 'emergency', 'maternity', 'paternity'];
        
        foreach ($employees as $employee) {
            // Create 2-3 leave requests per employee
            for ($i = 0; $i < rand(2, 3); $i++) {
                $startDate = now()->addDays(rand(1, 60));
                $days = rand(1, 5);
                $endDate = $startDate->copy()->addDays($days - 1);
                
                LeaveRequest::create([
                    'employee_id' => $employee->id,
                    'leave_type' => $leaveTypes[array_rand($leaveTypes)],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'days' => $days,
                    'reason' => 'Sample leave request for testing purposes',
                    'status' => ['pending', 'approved', 'rejected'][rand(0, 2)],
                    'applied_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }

    private function seedSalaryRecords()
    {
        $employees = Employee::all();
        
        // Create salary records for last 3 months
        for ($month = 3; $month >= 1; $month--) {
            $periodStart = now()->subMonths($month)->startOfMonth();
            $periodEnd = $periodStart->copy()->endOfMonth();
            
            foreach ($employees as $employee) {
                SalaryRecord::create([
                    'employee_id' => $employee->id,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'basic_salary' => $employee->salary,
                    'allowances' => $employee->salary * 0.1,
                    'overtime_amount' => rand(0, 500),
                    'deductions' => rand(0, 200),
                    'tax_deduction' => $employee->salary * 0.05,
                    'net_salary' => $employee->salary * 1.05 + rand(0, 300),
                    'working_days' => 22,
                    'worked_days' => rand(20, 22),
                    'leave_days' => rand(0, 2),
                    'overtime_hours' => rand(0, 20),
                    'status' => 'approved',
                    'prepared_by' => 1,
                    'approved_by' => 1,
                    'approved_at' => $periodEnd->addDays(5),
                ]);
            }
        }
    }

    private function seedPerformanceReviews()
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            PerformanceReview::create([
                'employee_id' => $employee->id,
                'reviewer_id' => $employees->where('id', '!=', $employee->id)->random()->id,
                'review_period' => 'annual',
                'review_date' => now()->subMonths(6),
                'technical_skills' => rand(3, 5),
                'communication_skills' => rand(3, 5),
                'teamwork' => rand(3, 5),
                'leadership' => rand(2, 5),
                'problem_solving' => rand(3, 5),
                'initiative' => rand(3, 5),
                'punctuality' => rand(3, 5),
                'quality_of_work' => rand(3, 5),
                'overall_rating' => rand(3, 5),
                'achievements' => 'Excellent performance throughout the year with consistent delivery of high-quality work.',
                'areas_for_improvement' => 'Continue developing leadership skills and take on more mentoring responsibilities.',
                'goals_next_period' => ['Complete advanced certification', 'Lead a major project', 'Mentor junior team members'],
                'reviewer_comments' => 'Strong performer with great potential for growth.',
                'employee_comments' => 'Thank you for the feedback. I look forward to taking on new challenges.',
                'status' => 'completed',
                'completed_at' => now()->subMonths(6)->addDays(7),
                'created_by' => 1,
            ]);
        }
    }

    private function seedAccountingData()
    {
        $this->command->info('Seeding accounting data...');

        // Seed Customers
        $customers = [
            ['name' => 'Acme Corporation', 'email' => 'contact@acme.com', 'company_name' => 'Acme Corporation', 'phone' => '+971-4-1234567'],
            ['name' => 'Global Tech Solutions', 'email' => 'info@globaltech.com', 'company_name' => 'Global Tech Solutions LLC', 'phone' => '+971-4-2345678'],
            ['name' => 'Emirates Trading Co.', 'email' => 'sales@emiratestrading.ae', 'company_name' => 'Emirates Trading Company', 'phone' => '+971-4-3456789'],
            ['name' => 'Dubai Innovations', 'email' => 'hello@dubaiinnovations.com', 'company_name' => 'Dubai Innovations FZ', 'phone' => '+971-4-4567890'],
            ['name' => 'Al Majid Group', 'email' => 'contact@almajidgroup.ae', 'company_name' => 'Al Majid Group LLC', 'phone' => '+971-4-5678901'],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                array_merge($customer, [
                    'address' => 'Dubai, UAE',
                    'city' => 'Dubai',
                    'country' => 'UAE',
                    'postal_code' => '12345',
                    'currency_id' => Currency::where('code', 'AED')->first()?->id ?? 1,
                    'tax_id' => Tax::where('code', 'VAT_AE')->first()?->id ?? 1,
                ])
            );
        }

        // Seed Vendors
        $vendors = [
            ['name' => 'Office Supplies Co.', 'email' => 'orders@officesupplies.ae', 'company_name' => 'Office Supplies Company'],
            ['name' => 'Tech Equipment LLC', 'email' => 'sales@techequipment.com', 'company_name' => 'Tech Equipment LLC'],
            ['name' => 'Facility Services', 'email' => 'info@facilityservices.ae', 'company_name' => 'Facility Services FZ'],
            ['name' => 'Marketing Agency', 'email' => 'hello@marketingagency.com', 'company_name' => 'Creative Marketing Agency'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(
                ['email' => $vendor['email']],
                array_merge($vendor, [
                    'phone' => '+971-4-' . rand(1000000, 9999999),
                    'address' => 'Dubai, UAE',
                    'city' => 'Dubai',
                    'country' => 'UAE',
                    'postal_code' => '12345',
                    'currency_id' => Currency::where('code', 'AED')->first()?->id ?? 1,
                    'tax_id' => Tax::where('code', 'VAT_AE')->first()?->id ?? 1,
                ])
            );
        }

        // Seed Invoices
        $this->seedInvoices();
        
        // Seed Expenses
        $this->seedExpenses();
    }

    private function seedInvoices()
    {
        $customers = Customer::all();
        $currency = Currency::where('code', 'AED')->first() ?? Currency::first();
        $tax = Tax::where('code', 'VAT_AE')->first() ?? Tax::first();
        
        for ($i = 1; $i <= 20; $i++) {
            $customer = $customers->random();
            $subtotal = rand(5000, 50000);
            $taxAmount = $subtotal * 0.05; // 5% VAT
            $total = $subtotal + $taxAmount;
            
            Invoice::create([
                'invoice_number' => 'INV-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'invoice_date' => now()->subDays(rand(1, 90)),
                'due_date' => now()->addDays(rand(15, 45)),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'currency_id' => $currency->id,
                'status' => ['draft', 'sent', 'paid', 'partial', 'overdue'][rand(0, 4)],
                'notes' => 'Sample invoice for testing purposes',
                'terms' => 'Payment due within 30 days',
            ]);
        }
    }

    private function seedExpenses()
    {
        $vendors = Vendor::all();
        $categories = ['office_supplies', 'equipment', 'utilities', 'marketing', 'travel', 'software'];
        $currency = Currency::where('code', 'AED')->first() ?? Currency::first();
        
        for ($i = 1; $i <= 30; $i++) {
            $vendor = $vendors->random();
            $amount = rand(500, 10000);
            
            Expense::create([
                'expense_number' => 'EXP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vendor_id' => $vendor->id,
                'expense_date' => now()->subDays(rand(1, 60)),
                'amount' => $amount,
                'currency_id' => $currency->id,
                'category' => $categories[array_rand($categories)],
                'description' => 'Sample expense for ' . $categories[array_rand($categories)],
                'status' => ['pending', 'approved', 'paid'][rand(0, 2)],
                'receipt_path' => null,
                'notes' => 'Sample expense for testing purposes',
            ]);
        }
    }
}
