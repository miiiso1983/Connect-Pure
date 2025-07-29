# HR Module API Documentation

## Overview

The HR Module provides a comprehensive REST API for managing human resources operations including employees, departments, roles, leave requests, attendance, and payroll. All endpoints support both English and Arabic languages with proper localization.

## Base URL
```
https://your-domain.com/modules/hr/
```

## Authentication
All API endpoints require authentication. Include the authentication token in the request headers:
```
Authorization: Bearer {your-token}
```

## Response Format
All API responses follow a consistent JSON format:

### Success Response
```json
{
    "success": true,
    "data": {
        // Response data
    },
    "message": "Operation completed successfully",
    "meta": {
        "pagination": {
            "current_page": 1,
            "total_pages": 10,
            "per_page": 15,
            "total": 150
        }
    }
}
```

### Error Response
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid",
        "details": {
            "email": ["The email field is required"]
        }
    }
}
```

## Employee Management

### List Employees
```http
GET /employees
```

**Query Parameters:**
- `search` (string): Search by name or email
- `department_id` (integer): Filter by department
- `role_id` (integer): Filter by role
- `employment_type` (string): Filter by employment type
- `status` (string): Filter by status
- `page` (integer): Page number for pagination
- `per_page` (integer): Items per page (max 100)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "employee_number": "EMP1001",
            "first_name": "Ahmed",
            "last_name": "Al-Rashid",
            "display_name": "Ahmed Al-Rashid",
            "email": "ahmed@company.com",
            "phone": "+966501234567",
            "department": {
                "id": 1,
                "name": "Information Technology",
                "name_ar": "تقنية المعلومات"
            },
            "role": {
                "id": 1,
                "name": "Software Engineer",
                "name_ar": "مهندس برمجيات"
            },
            "employment_type": "full_time",
            "status": "active",
            "hire_date": "2021-01-15",
            "basic_salary": 15000,
            "created_at": "2021-01-15T08:00:00Z",
            "updated_at": "2021-01-15T08:00:00Z"
        }
    ]
}
```

### Create Employee
```http
POST /employees
```

**Request Body:**
```json
{
    "first_name": "Ahmed",
    "last_name": "Al-Rashid",
    "first_name_ar": "أحمد",
    "last_name_ar": "الراشد",
    "email": "ahmed@company.com",
    "phone": "+966501234567",
    "mobile": "+966501234567",
    "date_of_birth": "1990-01-01",
    "gender": "male",
    "marital_status": "married",
    "nationality": "Saudi",
    "national_id": "1234567890",
    "department_id": 1,
    "role_id": 1,
    "manager_id": 2,
    "hire_date": "2025-01-01",
    "employment_type": "full_time",
    "basic_salary": 15000,
    "allowances": {
        "housing": 3000,
        "transport": 1000
    },
    "bank_name": "Saudi National Bank",
    "bank_account_number": "123456789",
    "iban": "SA1234567890123456789"
}
```

### Get Employee Details
```http
GET /employees/{id}
```

### Update Employee
```http
PUT /employees/{id}
```

### Delete Employee
```http
DELETE /employees/{id}
```

## Department Management

### List Departments
```http
GET /departments
```

### Create Department
```http
POST /departments
```

**Request Body:**
```json
{
    "name": "Information Technology",
    "name_ar": "تقنية المعلومات",
    "description": "Manages all technology operations",
    "description_ar": "إدارة جميع العمليات التقنية",
    "code": "IT",
    "manager_id": 1,
    "budget": 500000,
    "location": "Building A, Floor 3",
    "phone": "+966-11-123-4567",
    "email": "it@company.com"
}
```

### Get Department Details
```http
GET /departments/{id}
```

### Update Department
```http
PUT /departments/{id}
```

### Delete Department
```http
DELETE /departments/{id}
```

## Leave Management

### List Leave Requests
```http
GET /leave-requests
```

**Query Parameters:**
- `employee_id` (integer): Filter by employee
- `status` (string): Filter by status (pending, approved, rejected, cancelled)
- `leave_type` (string): Filter by leave type
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date

### Submit Leave Request
```http
POST /leave-requests
```

**Request Body:**
```json
{
    "employee_id": 1,
    "leave_type": "annual",
    "start_date": "2025-08-01",
    "end_date": "2025-08-05",
    "is_half_day": false,
    "reason": "Family vacation",
    "reason_ar": "إجازة عائلية",
    "contact_during_leave": "+966501234567"
}
```

### Approve Leave Request
```http
PATCH /leave-requests/{id}/approve
```

**Request Body:**
```json
{
    "approval_notes": "Approved for family time"
}
```

### Reject Leave Request
```http
PATCH /leave-requests/{id}/reject
```

**Request Body:**
```json
{
    "rejection_reason": "Busy period, please reschedule"
}
```

### Cancel Leave Request
```http
PATCH /leave-requests/{id}/cancel
```

## Attendance Management

### List Attendance Records
```http
GET /attendance
```

**Query Parameters:**
- `employee_id` (integer): Filter by employee
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date
- `status` (string): Filter by status

### Record Attendance
```http
POST /attendance
```

**Request Body:**
```json
{
    "employee_id": 1,
    "date": "2025-01-15",
    "scheduled_in": "09:00",
    "scheduled_out": "17:00",
    "actual_in": "09:15",
    "actual_out": "17:30",
    "status": "present",
    "location": "Office",
    "notes": "Late due to traffic"
}
```

### Check In
```http
POST /attendance/check-in
```

**Request Body:**
```json
{
    "employee_id": 1,
    "check_in_time": "09:15",
    "location": "Office",
    "latitude": 24.7136,
    "longitude": 46.6753
}
```

### Check Out
```http
POST /attendance/check-out
```

**Request Body:**
```json
{
    "employee_id": 1,
    "check_out_time": "17:30",
    "latitude": 24.7136,
    "longitude": 46.6753
}
```

## Payroll Management

### List Salary Records
```http
GET /payroll
```

**Query Parameters:**
- `employee_id` (integer): Filter by employee
- `year` (integer): Filter by year
- `month` (integer): Filter by month
- `status` (string): Filter by status

### Create Salary Record
```http
POST /payroll
```

**Request Body:**
```json
{
    "employee_id": 1,
    "year": 2025,
    "month": 1,
    "working_days": 22,
    "actual_working_days": 20,
    "basic_salary": 15000,
    "housing_allowance": 3000,
    "transport_allowance": 1000,
    "overtime_hours": 10,
    "overtime_amount": 500,
    "social_insurance": 1350,
    "income_tax": 750,
    "notes": "Regular monthly salary"
}
```

### Approve Salary Record
```http
PATCH /payroll/{id}/approve
```

### Mark as Paid
```http
PATCH /payroll/{id}/mark-paid
```

**Request Body:**
```json
{
    "payment_method": "bank_transfer",
    "payment_reference": "TXN123456789"
}
```

### Generate Payslip
```http
GET /payroll/{id}/payslip
```

### Post to Accounting
```http
POST /payroll/{id}/post-to-accounting
```

## Bulk Operations

### Generate Monthly Payroll
```http
POST /payroll/generate-monthly
```

**Request Body:**
```json
{
    "year": 2025,
    "month": 1
}
```

### Bulk Approve Salary Records
```http
POST /payroll/bulk-approve
```

**Request Body:**
```json
{
    "salary_record_ids": [1, 2, 3, 4, 5]
}
```

### Bulk Post to Accounting
```http
POST /payroll/bulk-post-to-accounting
```

**Request Body:**
```json
{
    "salary_record_ids": [1, 2, 3, 4, 5]
}
```

## Reports and Analytics

### Dashboard Statistics
```http
GET /api/dashboard/stats
```

### Employee Summary
```http
GET /attendance/employee/{id}/summary?year=2025&month=1
```

### Payroll Summary
```http
GET /payroll/reports/summary?year=2025&month=1
```

## Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `NOT_FOUND` | Resource not found |
| `UNAUTHORIZED` | Authentication required |
| `FORBIDDEN` | Insufficient permissions |
| `CONFLICT` | Resource conflict (e.g., duplicate email) |
| `INSUFFICIENT_BALANCE` | Insufficient leave balance |
| `OVERLAPPING_REQUEST` | Overlapping leave request exists |
| `INVALID_STATUS` | Invalid status transition |
| `ALREADY_POSTED` | Already posted to accounting |

## Rate Limiting
API requests are limited to 1000 requests per hour per authenticated user.

## Localization
All endpoints support localization through the `Accept-Language` header:
- `en` for English (default)
- `ar` for Arabic

Example:
```http
Accept-Language: ar
```

## Pagination
List endpoints support pagination with the following parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

## File Uploads
File uploads (profile photos, attachments) should be sent as multipart/form-data with a maximum file size of 5MB per file.

## Webhooks
The system supports webhooks for real-time notifications:
- Employee created/updated
- Leave request submitted/approved/rejected
- Attendance recorded
- Salary record approved/paid

Configure webhook URLs in the admin panel.
