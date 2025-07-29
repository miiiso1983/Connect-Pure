# Security Implementation Guide

## Overview

This guide provides comprehensive security measures for the HR and Accounting modules, covering authentication, authorization, data protection, input validation, and compliance with security best practices.

## Authentication & Authorization

### 1. Multi-Factor Authentication (MFA)

#### Implementation with Laravel Fortify
```php
// Install Laravel Fortify
composer require laravel/fortify

// Enable two-factor authentication
// config/fortify.php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

#### Custom MFA Middleware
```php
class RequireTwoFactorAuth
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        
        if ($user && !$user->hasEnabledTwoFactorAuthentication()) {
            if ($this->isAccessingHROrAccounting($request)) {
                return redirect()->route('two-factor.enable')
                    ->with('warning', 'Two-factor authentication is required for HR and Accounting modules.');
            }
        }

        return $next($request);
    }

    private function isAccessingHROrAccounting($request)
    {
        return $request->is('modules/hr/*') || $request->is('modules/accounting/*');
    }
}
```

### 2. Role-Based Access Control (RBAC)

#### Permission System
```php
// Create permissions
class CreateHRPermissions extends Migration
{
    public function up()
    {
        $permissions = [
            // HR Permissions
            'hr.view',
            'hr.employees.view',
            'hr.employees.create',
            'hr.employees.edit',
            'hr.employees.delete',
            'hr.departments.manage',
            'hr.roles.manage',
            'hr.leave.view',
            'hr.leave.approve',
            'hr.attendance.view',
            'hr.attendance.manage',
            'hr.payroll.view',
            'hr.payroll.process',
            'hr.payroll.approve',
            
            // Accounting Permissions
            'accounting.view',
            'accounting.entries.create',
            'accounting.entries.edit',
            'accounting.entries.delete',
            'accounting.reports.view',
            'accounting.settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
```

#### Role Definitions
```php
class CreateRoles extends Migration
{
    public function up()
    {
        // HR Roles
        $hrManager = Role::create(['name' => 'HR Manager']);
        $hrSpecialist = Role::create(['name' => 'HR Specialist']);
        $employee = Role::create(['name' => 'Employee']);
        
        // Accounting Roles
        $accountingManager = Role::create(['name' => 'Accounting Manager']);
        $accountant = Role::create(['name' => 'Accountant']);
        
        // Assign permissions
        $hrManager->givePermissionTo([
            'hr.view', 'hr.employees.view', 'hr.employees.create',
            'hr.employees.edit', 'hr.departments.manage',
            'hr.payroll.process', 'hr.payroll.approve'
        ]);

        $employee->givePermissionTo([
            'hr.view', 'hr.leave.view', 'hr.attendance.view'
        ]);
    }
}
```

### 3. API Authentication

#### Sanctum Token Authentication
```php
// API routes with Sanctum
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::prefix('api/hr')->group(function () {
        Route::get('/profile', [EmployeeController::class, 'apiProfile'])
            ->middleware('permission:hr.employees.view');
        
        Route::post('/attendance/check-in', [AttendanceController::class, 'apiCheckIn'])
            ->middleware('permission:hr.attendance.manage');
    });
});
```

## Data Protection & Encryption

### 1. Sensitive Data Encryption

#### Custom Encrypted Attributes
```php
trait EncryptedAttributes
{
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encrypted ?? [])) {
            $value = encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encrypted ?? []) && $value) {
            try {
                $value = decrypt($value);
            } catch (DecryptException $e) {
                // Handle decryption failure
                $value = null;
            }
        }

        return $value;
    }
}

// Employee model with encrypted fields
class Employee extends Model
{
    use EncryptedAttributes;

    protected $encrypted = [
        'national_id',
        'passport_number',
        'bank_account_number',
        'iban',
        'emergency_contact_phone'
    ];
}
```

### 2. Database Security

#### Column-Level Encryption
```sql
-- Create encrypted columns for sensitive data
ALTER TABLE hr_employees 
ADD COLUMN national_id_encrypted VARBINARY(255),
ADD COLUMN bank_account_encrypted VARBINARY(255);

-- Use MySQL's AES encryption functions
INSERT INTO hr_employees (national_id_encrypted) 
VALUES (AES_ENCRYPT('1234567890', 'encryption_key'));

SELECT AES_DECRYPT(national_id_encrypted, 'encryption_key') as national_id 
FROM hr_employees;
```

### 3. File Security

#### Secure File Upload
```php
class SecureFileUpload
{
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    private $maxFileSize = 5242880; // 5MB

    public function validateFile(UploadedFile $file): array
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            $errors[] = 'File size exceeds maximum allowed size of 5MB';
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            $errors[] = 'File type not allowed';
        }

        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'File extension not allowed';
        }

        // Scan for malware (if ClamAV is available)
        if ($this->isMalwareDetected($file)) {
            $errors[] = 'File contains malware';
        }

        return $errors;
    }

    public function secureStore(UploadedFile $file, string $directory): string
    {
        // Generate secure filename
        $filename = hash('sha256', $file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
        
        // Store with restricted permissions
        $path = $file->storeAs($directory, $filename, 'secure');
        
        // Set file permissions
        chmod(storage_path('app/secure/' . $directory . '/' . $filename), 0644);
        
        return $path;
    }

    private function isMalwareDetected(UploadedFile $file): bool
    {
        // Implement malware scanning logic
        // This could integrate with ClamAV or other antivirus solutions
        return false;
    }
}
```

## Input Validation & Sanitization

### 1. Comprehensive Validation Rules

#### Employee Validation
```php
class EmployeeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:hr_employees,email'],
            'phone' => ['nullable', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'national_id' => ['nullable', 'regex:/^\d{10}$/', 'unique:hr_employees,national_id'],
            'basic_salary' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'hire_date' => ['required', 'date', 'before_or_equal:today'],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'phone' => $this->sanitizePhone($this->phone),
            'email' => strtolower(trim($this->email)),
            'first_name' => $this->sanitizeName($this->first_name),
            'last_name' => $this->sanitizeName($this->last_name),
        ]);
    }

    private function sanitizePhone($phone)
    {
        return preg_replace('/[^+\d]/', '', $phone);
    }

    private function sanitizeName($name)
    {
        return trim(preg_replace('/[^a-zA-Z\s]/', '', $name));
    }
}
```

### 2. SQL Injection Prevention

#### Parameterized Queries
```php
// Always use parameter binding
class AttendanceRepository
{
    public function getEmployeeAttendance($employeeId, $startDate, $endDate)
    {
        return DB::select("
            SELECT date, status, total_hours 
            FROM hr_attendance 
            WHERE employee_id = ? 
            AND date BETWEEN ? AND ?
            ORDER BY date DESC
        ", [$employeeId, $startDate, $endDate]);
    }

    // Use Query Builder for complex queries
    public function getAttendanceStats($filters)
    {
        $query = DB::table('hr_attendance')
            ->select(DB::raw('
                COUNT(*) as total_records,
                COUNT(CASE WHEN status = "present" THEN 1 END) as present_count,
                AVG(total_hours) as avg_hours
            '));

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        return $query->first();
    }
}
```

## Session Security

### 1. Secure Session Configuration

#### Session Settings
```php
// config/session.php
return [
    'driver' => env('SESSION_DRIVER', 'database'),
    'lifetime' => env('SESSION_LIFETIME', 120), // 2 hours
    'expire_on_close' => true,
    'encrypt' => true,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION', null),
    'table' => 'sessions',
    'store' => env('SESSION_STORE', null),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'laravel_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'strict',
];
```

### 2. Session Hijacking Prevention

#### Session Regeneration Middleware
```php
class RegenerateSession
{
    public function handle($request, Closure $next)
    {
        // Regenerate session ID on sensitive operations
        if ($this->isSensitiveOperation($request)) {
            $request->session()->regenerate();
        }

        return $next($request);
    }

    private function isSensitiveOperation($request)
    {
        $sensitiveRoutes = [
            'modules.hr.employees.store',
            'modules.hr.payroll.approve',
            'modules.accounting.entries.store',
        ];

        return in_array($request->route()->getName(), $sensitiveRoutes);
    }
}
```

## Audit Logging

### 1. Comprehensive Audit Trail

#### Audit Log Model
```php
class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
```

#### Auditable Trait
```php
trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    public function logActivity($action)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'old_values' => $this->getOriginal(),
            'new_values' => $this->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }
}

// Use in models
class Employee extends Model
{
    use Auditable;
}
```

## Security Headers

### 1. HTTP Security Headers

#### Security Headers Middleware
```php
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
```

## Rate Limiting

### 1. API Rate Limiting

#### Custom Rate Limiter
```php
class HRRateLimiter
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            RateLimiter::retriesLeft($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature($request)
    {
        return sha1(
            $request->method() .
            '|' . $request->server('SERVER_NAME') .
            '|' . $request->path() .
            '|' . $request->ip()
        );
    }
}
```

## Compliance & Privacy

### 1. GDPR Compliance

#### Data Export
```php
class GDPRController extends Controller
{
    public function exportPersonalData(Employee $employee)
    {
        $this->authorize('export-personal-data', $employee);

        $data = [
            'personal_information' => $employee->only([
                'first_name', 'last_name', 'email', 'phone', 'date_of_birth'
            ]),
            'employment_information' => $employee->only([
                'employee_number', 'hire_date', 'employment_type', 'status'
            ]),
            'leave_requests' => $employee->leaveRequests()->get(),
            'attendance_records' => $employee->attendance()->get(),
            'salary_records' => $employee->salaryRecords()->get(),
        ];

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="personal_data.json"');
    }

    public function deletePersonalData(Employee $employee)
    {
        $this->authorize('delete-personal-data', $employee);

        // Anonymize instead of delete to maintain referential integrity
        $employee->update([
            'first_name' => 'Deleted',
            'last_name' => 'User',
            'email' => 'deleted_' . $employee->id . '@example.com',
            'phone' => null,
            'national_id' => null,
            'passport_number' => null,
            'address' => null,
            'bank_account_number' => null,
            'iban' => null,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'gdpr_deletion',
            'model_type' => Employee::class,
            'model_id' => $employee->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
```

## Security Monitoring

### 1. Intrusion Detection

#### Suspicious Activity Monitor
```php
class SecurityMonitor
{
    public function detectSuspiciousActivity($request)
    {
        $suspiciousPatterns = [
            'sql_injection' => '/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bDELETE\b|\bDROP\b)/i',
            'xss_attempt' => '/<script|javascript:|on\w+\s*=/i',
            'path_traversal' => '/\.\.[\/\\]/',
            'command_injection' => '/[;&|`$(){}]/i',
        ];

        foreach ($suspiciousPatterns as $type => $pattern) {
            if (preg_match($pattern, $request->getContent()) || 
                preg_match($pattern, $request->getQueryString())) {
                
                $this->logSecurityIncident($type, $request);
                return true;
            }
        }

        return false;
    }

    private function logSecurityIncident($type, $request)
    {
        Log::channel('security')->warning('Suspicious activity detected', [
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'payload' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Optionally block IP after multiple incidents
        $this->checkForIPBlocking($request->ip());
    }
}
```

This security implementation guide provides a comprehensive framework for securing the HR and Accounting modules, covering all major security aspects from authentication to compliance.
