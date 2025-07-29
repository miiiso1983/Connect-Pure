# HR Module Documentation

## Overview

The HR (Human Resources) module is a comprehensive employee management system built for Laravel applications. It provides complete functionality for managing employees, departments, roles, leave requests, attendance tracking, and payroll processing with full bilingual support (English/Arabic).

## Features

### 🏢 Core HR Management
- **Employee Directory**: Complete employee profiles with personal and employment information
- **Department Management**: Organize employees into departments with budget tracking
- **Role Management**: Define job roles with salary ranges and responsibilities
- **Hierarchical Structure**: Manager-subordinate relationships

### 📅 Leave Management
- **Leave Requests**: Submit, approve, and track various types of leave
- **Leave Types**: Annual, sick, emergency, maternity, paternity, unpaid, study, hajj, bereavement
- **Approval Workflow**: Manager approval with notes and rejection reasons
- **Leave Balance Tracking**: Automatic balance deduction and restoration

### ⏰ Attendance System
- **Daily Attendance**: Check-in/check-out with time tracking
- **Overtime Calculation**: Automatic overtime hours calculation
- **Attendance Reports**: Monthly summaries and analytics
- **Location Tracking**: GPS coordinates for remote check-ins
- **Approval System**: Manager approval for attendance records

### 💰 Payroll Management
- **Salary Records**: Comprehensive payroll processing
- **Allowances & Deductions**: Housing, transport, social insurance, taxes
- **Payslip Generation**: Detailed payslips with all components
- **Accounting Integration**: Automatic journal entries and expense records
- **Bulk Processing**: Generate monthly payroll for all employees

### 🌐 Bilingual Support
- **English/Arabic**: Complete translation support
- **RTL Layout**: Right-to-left layout for Arabic
- **Localized Data**: Arabic names and descriptions

### 📊 Reporting & Analytics
- **Dashboard**: Real-time HR metrics and statistics
- **Department Analytics**: Budget utilization and performance
- **Attendance Reports**: Trends and summaries
- **Payroll Reports**: Cost analysis and comparisons

## Installation & Setup

### 1. Database Migration
```bash
php artisan migrate
```

### 2. Seed Sample Data
```bash
php artisan db:seed --class=HRSeeder
```

### 3. Access the Module
Navigate to: `http://your-domain/modules/hr/`

## Database Schema

### Core Tables

#### hr_departments
- Department information with budget and location
- Manager assignment and contact details
- Bilingual name and description support

#### hr_roles
- Job roles with salary ranges and levels
- Responsibilities and requirements
- Department association

#### hr_employees
- Complete employee profiles
- Personal, employment, and contact information
- Salary details and leave balances
- Manager relationships

#### hr_leave_requests
- Leave request management
- Approval workflow tracking
- File attachments support

#### hr_attendance
- Daily attendance records
- Time tracking and overtime calculation
- Location and approval data

#### hr_salary_records
- Monthly payroll records
- Detailed salary components
- Accounting integration fields

## API Endpoints

### Employee Management
```
GET    /modules/hr/employees              # List employees
POST   /modules/hr/employees              # Create employee
GET    /modules/hr/employees/{id}         # View employee
PUT    /modules/hr/employees/{id}         # Update employee
DELETE /modules/hr/employees/{id}         # Delete employee
```

### Department Management
```
GET    /modules/hr/departments            # List departments
POST   /modules/hr/departments            # Create department
GET    /modules/hr/departments/{id}       # View department
PUT    /modules/hr/departments/{id}       # Update department
DELETE /modules/hr/departments/{id}       # Delete department
```

### Leave Management
```
GET    /modules/hr/leave-requests         # List leave requests
POST   /modules/hr/leave-requests         # Submit leave request
PATCH  /modules/hr/leave-requests/{id}/approve  # Approve request
PATCH  /modules/hr/leave-requests/{id}/reject   # Reject request
```

### Attendance
```
GET    /modules/hr/attendance             # List attendance
POST   /modules/hr/attendance/check-in    # Check in
POST   /modules/hr/attendance/check-out   # Check out
POST   /modules/hr/attendance/generate-daily  # Generate daily records
```

### Payroll
```
GET    /modules/hr/payroll                # List salary records
POST   /modules/hr/payroll                # Create salary record
PATCH  /modules/hr/payroll/{id}/approve   # Approve salary
POST   /modules/hr/payroll/generate-monthly  # Generate monthly payroll
```

## Configuration

### Leave Policies
Configure leave types and balances in the settings:
- Annual leave: 21 days (default)
- Sick leave: 30 days (default)
- Emergency leave: 5 days (default)

### Attendance Policies
- Standard working hours: 9:00 AM - 5:00 PM
- Overtime calculation: After 8 hours
- Weekend days: Friday and Saturday (Saudi Arabia)

### Payroll Settings
- Social insurance rate: 9%
- Income tax rate: 5%
- Payment methods: Bank transfer, cash, check

## Accounting Integration

The HR module integrates seamlessly with the accounting system:

### Automatic Journal Entries
When salary records are posted to accounting:
- **Debit**: Salary Expense Account (6100)
- **Credit**: Social Insurance Payable (2200)
- **Credit**: Income Tax Payable (2210)
- **Credit**: Salary Payable (2100)

### Expense Records
Each salary payment creates an expense record with:
- Employee as vendor
- Salary expense account
- Payment details and references

### Chart of Accounts
The system automatically creates required accounts:
- 6100: Salary Expense
- 2100: Salary Payable
- 2200: Social Insurance Payable
- 2210: Income Tax Payable
- 2220: Other Deductions Payable

## Sample Data

The seeder creates:
- **5 Departments**: IT, HR, Finance, Sales, Operations
- **10 Roles**: Various positions with salary ranges
- **3 Employees**: Sample employees with complete profiles
- **Attendance Records**: 30 days of sample attendance
- **Leave Requests**: Sample leave requests
- **Salary Records**: Current and previous month payroll

## Usage Examples

### Creating an Employee
```php
$employee = Employee::create([
    'employee_number' => Employee::generateEmployeeNumber(),
    'first_name' => 'Ahmed',
    'last_name' => 'Al-Rashid',
    'email' => 'ahmed@company.com',
    'department_id' => 1,
    'role_id' => 2,
    'hire_date' => now(),
    'basic_salary' => 15000,
    'employment_type' => 'full_time',
    'status' => 'active',
]);
```

### Processing Leave Request
```php
$leaveRequest = LeaveRequest::create([
    'employee_id' => 1,
    'leave_type' => 'annual',
    'start_date' => '2025-08-01',
    'end_date' => '2025-08-05',
    'total_days' => 5,
    'reason' => 'Family vacation',
]);

// Approve the request
$leaveRequest->approve($managerId, 'Approved for family time');
```

### Recording Attendance
```php
$attendance = Attendance::create([
    'employee_id' => 1,
    'date' => now()->toDateString(),
    'scheduled_in' => '09:00:00',
    'scheduled_out' => '17:00:00',
]);

// Check in
$attendance->checkIn(now());

// Check out
$attendance->checkOut(now()->addHours(8));
```

### Generating Payroll
```php
$salaryRecord = SalaryRecord::create([
    'employee_id' => 1,
    'year' => now()->year,
    'month' => now()->month,
    'basic_salary' => 15000,
    'housing_allowance' => 3000,
    'transport_allowance' => 1000,
]);

$salaryRecord->calculateNetSalary();
$salaryRecord->postToAccounting();
```

## Security Features

- **Role-based Access**: Different permissions for HR staff and managers
- **Data Validation**: Comprehensive input validation
- **File Upload Security**: Secure handling of profile photos and attachments
- **Audit Trail**: Track all changes and approvals

## Performance Optimization

- **Database Indexing**: Optimized queries with proper indexes
- **Eager Loading**: Efficient relationship loading
- **Pagination**: Large datasets are paginated
- **Caching**: Dashboard statistics are cached

## Troubleshooting

### Common Issues

1. **Migration Errors**: Ensure accounting module is migrated first
2. **Seeder Failures**: Check for existing data conflicts
3. **Permission Errors**: Verify file upload permissions
4. **Calculation Errors**: Check salary calculation methods

### Debug Mode
Enable debug mode in `.env`:
```
APP_DEBUG=true
```

## Support

For technical support or feature requests:
- Check the documentation
- Review the code comments
- Test with sample data
- Verify database relationships

## Version History

- **v1.0**: Initial release with core HR functionality
- **v1.1**: Added accounting integration
- **v1.2**: Enhanced bilingual support
- **v1.3**: Improved reporting and analytics
