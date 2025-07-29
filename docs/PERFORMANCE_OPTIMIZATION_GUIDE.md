# Performance Optimization Guide

## Overview

This guide provides comprehensive strategies for optimizing the performance of both the HR and Accounting modules in the Laravel application. These optimizations focus on database queries, caching, indexing, and application-level improvements.

## Database Optimizations

### 1. Database Indexing

#### HR Module Indexes
```sql
-- Employee table indexes
CREATE INDEX idx_hr_employees_department_id ON hr_employees(department_id);
CREATE INDEX idx_hr_employees_role_id ON hr_employees(role_id);
CREATE INDEX idx_hr_employees_manager_id ON hr_employees(manager_id);
CREATE INDEX idx_hr_employees_status ON hr_employees(status);
CREATE INDEX idx_hr_employees_hire_date ON hr_employees(hire_date);
CREATE INDEX idx_hr_employees_email ON hr_employees(email);
CREATE INDEX idx_hr_employees_employee_number ON hr_employees(employee_number);

-- Leave requests indexes
CREATE INDEX idx_hr_leave_requests_employee_id ON hr_leave_requests(employee_id);
CREATE INDEX idx_hr_leave_requests_status ON hr_leave_requests(status);
CREATE INDEX idx_hr_leave_requests_leave_type ON hr_leave_requests(leave_type);
CREATE INDEX idx_hr_leave_requests_start_date ON hr_leave_requests(start_date);
CREATE INDEX idx_hr_leave_requests_end_date ON hr_leave_requests(end_date);
CREATE INDEX idx_hr_leave_requests_approver_id ON hr_leave_requests(approver_id);

-- Attendance indexes
CREATE INDEX idx_hr_attendance_employee_id ON hr_attendance(employee_id);
CREATE INDEX idx_hr_attendance_date ON hr_attendance(date);
CREATE INDEX idx_hr_attendance_status ON hr_attendance(status);
CREATE INDEX idx_hr_attendance_employee_date ON hr_attendance(employee_id, date);

-- Salary records indexes
CREATE INDEX idx_hr_salary_records_employee_id ON hr_salary_records(employee_id);
CREATE INDEX idx_hr_salary_records_year_month ON hr_salary_records(year, month);
CREATE INDEX idx_hr_salary_records_status ON hr_salary_records(status);
CREATE INDEX idx_hr_salary_records_period_end ON hr_salary_records(period_end);
```

#### Accounting Module Indexes
```sql
-- Journal entries indexes
CREATE INDEX idx_journal_entries_entry_date ON journal_entries(entry_date);
CREATE INDEX idx_journal_entries_status ON journal_entries(status);
CREATE INDEX idx_journal_entries_type ON journal_entries(type);
CREATE INDEX idx_journal_entries_reference ON journal_entries(reference);

-- Journal entry lines indexes
CREATE INDEX idx_journal_entry_lines_entry_id ON journal_entry_lines(journal_entry_id);
CREATE INDEX idx_journal_entry_lines_account_id ON journal_entry_lines(account_id);
CREATE INDEX idx_journal_entry_lines_debit ON journal_entry_lines(debit);
CREATE INDEX idx_journal_entry_lines_credit ON journal_entry_lines(credit);

-- Contacts indexes
CREATE INDEX idx_contacts_email ON contacts(email);
CREATE INDEX idx_contacts_status ON contacts(status);
CREATE INDEX idx_contacts_priority ON contacts(priority);
CREATE INDEX idx_contacts_source ON contacts(source);
CREATE INDEX idx_contacts_assigned_to ON contacts(assigned_to);
```

### 2. Query Optimization

#### Eager Loading Relationships
```php
// Instead of N+1 queries
$employees = Employee::all();
foreach ($employees as $employee) {
    echo $employee->department->name; // N+1 problem
}

// Use eager loading
$employees = Employee::with(['department', 'role', 'manager'])->get();
```

#### Optimized Employee Queries
```php
// Employee list with optimized loading
public function index(Request $request)
{
    $query = Employee::with([
        'department:id,name,name_ar',
        'role:id,name,name_ar,level',
        'manager:id,first_name,last_name'
    ]);

    // Use select to limit columns
    $query->select([
        'id', 'employee_number', 'first_name', 'last_name',
        'email', 'department_id', 'role_id', 'manager_id',
        'status', 'hire_date', 'basic_salary'
    ]);

    return $query->paginate(15);
}
```

#### Optimized Dashboard Queries
```php
// Use raw queries for complex aggregations
public function getDashboardStats()
{
    $stats = DB::select("
        SELECT 
            COUNT(*) as total_employees,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_employees,
            AVG(CASE WHEN status = 'active' THEN basic_salary END) as avg_salary,
            SUM(CASE WHEN status = 'active' THEN basic_salary END) as total_salary
        FROM hr_employees
    ")[0];

    return $stats;
}
```

### 3. Database Connection Optimization

#### Connection Pooling
```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::ATTR_PERSISTENT => true, // Enable persistent connections
    ]) : [],
],
```

## Caching Strategies

### 1. Model Caching

#### Employee Model Caching
```php
class Employee extends Model
{
    public function getDepartmentAttribute()
    {
        return Cache::remember(
            "employee.{$this->id}.department",
            3600, // 1 hour
            fn() => $this->belongsTo(Department::class)->first()
        );
    }

    public static function getActiveCount()
    {
        return Cache::remember(
            'employees.active.count',
            1800, // 30 minutes
            fn() => static::where('status', 'active')->count()
        );
    }
}
```

#### Dashboard Caching
```php
class DashboardController extends Controller
{
    public function getDashboardData()
    {
        return Cache::remember(
            'hr.dashboard.data',
            600, // 10 minutes
            function () {
                return [
                    'total_employees' => Employee::count(),
                    'active_employees' => Employee::active()->count(),
                    'departments_count' => Department::active()->count(),
                    'pending_leave_requests' => LeaveRequest::pending()->count(),
                    // ... other stats
                ];
            }
        );
    }
}
```

### 2. Query Result Caching

#### Attendance Summary Caching
```php
public function getAttendanceSummary($employeeId, $year, $month)
{
    $cacheKey = "attendance.summary.{$employeeId}.{$year}.{$month}";
    
    return Cache::remember($cacheKey, 3600, function () use ($employeeId, $year, $month) {
        return Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw('
                COUNT(*) as total_days,
                COUNT(CASE WHEN status = "present" THEN 1 END) as present_days,
                COUNT(CASE WHEN status = "absent" THEN 1 END) as absent_days,
                COUNT(CASE WHEN status = "late" THEN 1 END) as late_days,
                SUM(total_hours) as total_hours,
                SUM(overtime_hours) as overtime_hours
            ')
            ->first();
    });
}
```

### 3. Cache Invalidation

#### Automatic Cache Clearing
```php
class Employee extends Model
{
    protected static function booted()
    {
        static::saved(function ($employee) {
            Cache::forget('employees.active.count');
            Cache::forget('hr.dashboard.data');
            Cache::forget("employee.{$employee->id}.department");
        });

        static::deleted(function ($employee) {
            Cache::forget('employees.active.count');
            Cache::forget('hr.dashboard.data');
        });
    }
}
```

## Application-Level Optimizations

### 1. Pagination Optimization

#### Cursor-Based Pagination for Large Datasets
```php
public function index(Request $request)
{
    // For very large datasets, use cursor pagination
    $employees = Employee::with(['department', 'role'])
        ->orderBy('id')
        ->cursorPaginate(50);

    return $employees;
}
```

### 2. Bulk Operations

#### Bulk Insert Optimization
```php
public function createBulkAttendance($date, $employees)
{
    $attendanceData = [];
    
    foreach ($employees as $employee) {
        $attendanceData[] = [
            'employee_id' => $employee->id,
            'date' => $date,
            'scheduled_in' => '09:00:00',
            'scheduled_out' => '17:00:00',
            'status' => 'absent',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Bulk insert instead of individual inserts
    Attendance::insert($attendanceData);
}
```

### 3. Memory Management

#### Chunk Processing for Large Datasets
```php
public function processLargeDataset()
{
    Employee::chunk(100, function ($employees) {
        foreach ($employees as $employee) {
            // Process each employee
            $this->processEmployee($employee);
        }
    });
}
```

## Frontend Optimizations

### 1. AJAX Loading

#### Lazy Loading for Dashboard Components
```javascript
// Load dashboard components asynchronously
document.addEventListener('DOMContentLoaded', function() {
    // Load attendance chart
    fetch('/modules/hr/api/dashboard/attendance-chart')
        .then(response => response.json())
        .then(data => renderAttendanceChart(data));

    // Load department stats
    fetch('/modules/hr/api/dashboard/department-stats')
        .then(response => response.json())
        .then(data => renderDepartmentStats(data));
});
```

### 2. Data Table Optimization

#### Server-Side Processing for Large Tables
```javascript
$('#employees-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/modules/hr/employees/datatable',
        type: 'POST'
    },
    columns: [
        { data: 'employee_number', name: 'employee_number' },
        { data: 'name', name: 'first_name' },
        { data: 'department', name: 'department.name' },
        { data: 'role', name: 'role.name' },
        { data: 'status', name: 'status' }
    ]
});
```

## File Storage Optimization

### 1. Profile Photo Optimization

#### Image Resizing and Compression
```php
public function storeProfilePhoto(UploadedFile $file)
{
    $image = Image::make($file);
    
    // Resize and compress
    $image->resize(300, 300, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    })->encode('jpg', 80);

    $filename = 'profile_' . uniqid() . '.jpg';
    $path = 'employees/photos/' . $filename;
    
    Storage::disk('public')->put($path, $image->stream());
    
    return $path;
}
```

## Monitoring and Profiling

### 1. Query Monitoring

#### Log Slow Queries
```php
// config/logging.php
'channels' => [
    'slow_queries' => [
        'driver' => 'single',
        'path' => storage_path('logs/slow_queries.log'),
        'level' => 'debug',
    ],
],

// AppServiceProvider.php
public function boot()
{
    DB::listen(function ($query) {
        if ($query->time > 1000) { // Log queries taking more than 1 second
            Log::channel('slow_queries')->debug('Slow query detected', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time
            ]);
        }
    });
}
```

### 2. Performance Metrics

#### Custom Performance Middleware
```php
class PerformanceMiddleware
{
    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;

        Log::info('Performance metrics', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'peak_memory' => memory_get_peak_usage()
        ]);

        return $response;
    }
}
```

## Production Deployment Optimizations

### 1. Opcache Configuration
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

### 2. Laravel Optimizations
```bash
# Production optimization commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

# Enable JIT compilation (PHP 8.0+)
opcache.jit_buffer_size=256M
opcache.jit=1255
```

### 3. Database Connection Pooling
```bash
# Use connection pooling with tools like PgBouncer or ProxySQL
# Configure max connections based on your server capacity
```

## Monitoring Tools

### 1. Application Performance Monitoring
- **Laravel Telescope**: For development and staging
- **New Relic**: For production monitoring
- **Blackfire**: For profiling and optimization

### 2. Database Monitoring
- **MySQL Performance Schema**: Built-in MySQL monitoring
- **Percona Monitoring**: Advanced MySQL monitoring
- **Query Analyzer**: Identify slow queries

### 3. Cache Monitoring
- **Redis Monitor**: Monitor Redis performance
- **Memcached Stats**: Monitor Memcached usage

## Performance Benchmarks

### Target Performance Metrics
- **Page Load Time**: < 2 seconds
- **API Response Time**: < 500ms
- **Database Query Time**: < 100ms
- **Memory Usage**: < 128MB per request
- **Cache Hit Ratio**: > 90%

### Load Testing
```bash
# Use Apache Bench for basic load testing
ab -n 1000 -c 10 http://your-domain.com/modules/hr/

# Use Artillery for more advanced testing
artillery quick --count 10 --num 100 http://your-domain.com/modules/hr/
```

This performance optimization guide provides a comprehensive approach to improving the application's performance across all layers, from database to frontend, ensuring optimal user experience even with large datasets.
