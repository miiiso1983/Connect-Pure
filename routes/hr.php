<?php

use Illuminate\Support\Facades\Route;
use App\Modules\HR\Controllers\DashboardController;
use App\Modules\HR\Controllers\EmployeeController;
use App\Modules\HR\Controllers\DepartmentController;
use App\Modules\HR\Controllers\RoleController;
use App\Modules\HR\Controllers\LeaveRequestController;
use App\Modules\HR\Controllers\AttendanceController;
use App\Modules\HR\Controllers\SalaryRecordController;

/*
|--------------------------------------------------------------------------
| HR Module Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the HR (Human Resources) module.
| All routes are prefixed with 'modules/hr' and use the 'web' middleware.
|
*/

Route::prefix('modules/hr')->name('modules.hr.')->middleware(['web'])->group(function () {
    
    // HR Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/quick-stats', [DashboardController::class, 'quickStats'])->name('quick-stats');
    Route::get('/department-performance', [DashboardController::class, 'departmentPerformance'])->name('department-performance');

    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        
        // AJAX routes
        Route::get('/roles/by-department', [EmployeeController::class, 'getRolesByDepartment'])->name('roles.by-department');
        
        // Export
        Route::get('/export/csv', [EmployeeController::class, 'export'])->name('export');
    });

    // Department Management
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        
        // Additional actions
        Route::patch('/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{department}/performance', [DepartmentController::class, 'performance'])->name('performance');
        Route::get('/{department}/budget-analysis', [DepartmentController::class, 'budgetAnalysis'])->name('budget-analysis');
        
        // AJAX routes
        Route::get('/select/options', [DepartmentController::class, 'getForSelect'])->name('select.options');
        
        // Export
        Route::get('/export/csv', [DepartmentController::class, 'export'])->name('export');
    });

    // Role Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        
        // Additional actions
        Route::patch('/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('toggle-status');
        
        // AJAX routes
        Route::get('/by-department/{department}', [RoleController::class, 'getByDepartment'])->name('by-department');
        Route::get('/vacant/list', [RoleController::class, 'getVacantRoles'])->name('vacant.list');
        
        // Export
        Route::get('/export/csv', [RoleController::class, 'export'])->name('export');
    });

    // Leave Management
    Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
        Route::get('/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('show');
        Route::get('/{leaveRequest}/edit', [LeaveRequestController::class, 'edit'])->name('edit');
        Route::put('/{leaveRequest}', [LeaveRequestController::class, 'update'])->name('update');
        Route::delete('/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('destroy');
        
        // Approval actions
        Route::patch('/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('approve');
        Route::patch('/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('reject');
        Route::patch('/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('cancel');
        
        // File downloads
        Route::get('/{leaveRequest}/attachments/{index}/download', [LeaveRequestController::class, 'downloadAttachment'])->name('attachments.download');
        
        // AJAX routes
        Route::get('/employee/leave-balance', [LeaveRequestController::class, 'getEmployeeLeaveBalance'])->name('employee.leave-balance');
        
        // Export
        Route::get('/export/csv', [LeaveRequestController::class, 'export'])->name('export');
    });

    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [AttendanceController::class, 'create'])->name('create');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
        Route::get('/{attendance}', [AttendanceController::class, 'show'])->name('show');
        Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
        
        // Check-in/Check-out actions
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        
        // Approval
        Route::patch('/{attendance}/approve', [AttendanceController::class, 'approve'])->name('approve');
        
        // Bulk operations
        Route::post('/generate-daily', [AttendanceController::class, 'generateDaily'])->name('generate-daily');
        
        // Reports and data
        Route::get('/employee/{employee}/summary', [AttendanceController::class, 'employeeSummary'])->name('employee.summary');
        Route::get('/calendar/data', [AttendanceController::class, 'calendar'])->name('calendar.data');
        
        // Export
        Route::get('/export/csv', [AttendanceController::class, 'export'])->name('export');
    });

    // Salary Records & Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [SalaryRecordController::class, 'index'])->name('index');
        Route::get('/create', [SalaryRecordController::class, 'create'])->name('create');
        Route::post('/', [SalaryRecordController::class, 'store'])->name('store');
        Route::get('/{salaryRecord}', [SalaryRecordController::class, 'show'])->name('show');
        Route::get('/{salaryRecord}/edit', [SalaryRecordController::class, 'edit'])->name('edit');
        Route::put('/{salaryRecord}', [SalaryRecordController::class, 'update'])->name('update');
        Route::delete('/{salaryRecord}', [SalaryRecordController::class, 'destroy'])->name('destroy');
        
        // Payroll actions
        Route::patch('/{salaryRecord}/approve', [SalaryRecordController::class, 'approve'])->name('approve');
        Route::patch('/{salaryRecord}/mark-paid', [SalaryRecordController::class, 'markAsPaid'])->name('mark-paid');
        Route::patch('/{salaryRecord}/cancel', [SalaryRecordController::class, 'cancel'])->name('cancel');
        
        // Payslip and reports
        Route::get('/{salaryRecord}/payslip', [SalaryRecordController::class, 'generatePayslip'])->name('payslip');
        Route::get('/{salaryRecord}/payslip/pdf', [SalaryRecordController::class, 'downloadPayslip'])->name('payslip.pdf');
        
        // Bulk operations
        Route::post('/generate-monthly', [SalaryRecordController::class, 'generateMonthly'])->name('generate-monthly');
        Route::post('/bulk-approve', [SalaryRecordController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-pay', [SalaryRecordController::class, 'bulkPay'])->name('bulk-pay');
        
        // Accounting integration
        Route::post('/{salaryRecord}/post-to-accounting', [SalaryRecordController::class, 'postToAccounting'])->name('post-to-accounting');
        Route::post('/bulk-post-to-accounting', [SalaryRecordController::class, 'bulkPostToAccounting'])->name('bulk-post-to-accounting');
        
        // Reports
        Route::get('/reports/summary', [SalaryRecordController::class, 'payrollSummary'])->name('reports.summary');
        Route::get('/reports/comparison', [SalaryRecordController::class, 'payrollComparison'])->name('reports.comparison');
        
        // Export
        Route::get('/export/csv', [SalaryRecordController::class, 'export'])->name('export');
        Route::get('/export/payroll-summary', [SalaryRecordController::class, 'exportPayrollSummary'])->name('export.payroll-summary');
    });

    // Additional HR Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/employee-summary', [DashboardController::class, 'employeeSummaryReport'])->name('employee-summary');
        Route::get('/department-analysis', [DashboardController::class, 'departmentAnalysisReport'])->name('department-analysis');
        Route::get('/leave-analysis', [DashboardController::class, 'leaveAnalysisReport'])->name('leave-analysis');
        Route::get('/attendance-summary', [DashboardController::class, 'attendanceSummaryReport'])->name('attendance-summary');
        Route::get('/payroll-analysis', [DashboardController::class, 'payrollAnalysisReport'])->name('payroll-analysis');
    });

    // Settings and Configuration
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [DashboardController::class, 'settings'])->name('index');
        Route::post('/update', [DashboardController::class, 'updateSettings'])->name('update');
        
        // Leave policies
        Route::get('/leave-policies', [DashboardController::class, 'leavePolicies'])->name('leave-policies');
        Route::post('/leave-policies', [DashboardController::class, 'updateLeavePolicies'])->name('leave-policies.update');
        
        // Attendance policies
        Route::get('/attendance-policies', [DashboardController::class, 'attendancePolicies'])->name('attendance-policies');
        Route::post('/attendance-policies', [DashboardController::class, 'updateAttendancePolicies'])->name('attendance-policies.update');
        
        // Payroll settings
        Route::get('/payroll-settings', [DashboardController::class, 'payrollSettings'])->name('payroll-settings');
        Route::post('/payroll-settings', [DashboardController::class, 'updatePayrollSettings'])->name('payroll-settings.update');
    });

    // API Routes for mobile/external access
    Route::prefix('api')->name('api.')->group(function () {
        // Employee self-service
        Route::get('/employee/{employee}/profile', [EmployeeController::class, 'apiProfile'])->name('employee.profile');
        Route::get('/employee/{employee}/leave-balance', [EmployeeController::class, 'apiLeaveBalance'])->name('employee.leave-balance');
        Route::get('/employee/{employee}/attendance-summary', [EmployeeController::class, 'apiAttendanceSummary'])->name('employee.attendance-summary');
        
        // Quick attendance
        Route::post('/attendance/quick-check-in', [AttendanceController::class, 'apiQuickCheckIn'])->name('attendance.quick-check-in');
        Route::post('/attendance/quick-check-out', [AttendanceController::class, 'apiQuickCheckOut'])->name('attendance.quick-check-out');
        
        // Leave requests
        Route::post('/leave-requests/quick-submit', [LeaveRequestController::class, 'apiQuickSubmit'])->name('leave-requests.quick-submit');
        Route::get('/leave-requests/employee/{employee}', [LeaveRequestController::class, 'apiEmployeeRequests'])->name('leave-requests.employee');
        
        // Dashboard data
        Route::get('/dashboard/stats', [DashboardController::class, 'apiDashboardStats'])->name('dashboard.stats');
        Route::get('/dashboard/charts', [DashboardController::class, 'apiDashboardCharts'])->name('dashboard.charts');
    });
});

// HR routes are included in web.php
