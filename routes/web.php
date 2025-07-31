<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Modules\CRM\Controllers\CRMController;
use App\Modules\Support\Controllers\SupportController;

use App\Modules\Performance\Controllers\PerformanceController;
use App\Modules\HR\Controllers\HRController;

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
Route::post('/switch-language', [DashboardController::class, 'switchLanguage'])->name('switch-language');

// Theme test route (for testing dual theme system)
Route::get('/theme-test', function () {
    return view('theme-test');
})->name('theme.test');

// Include authentication routes
require __DIR__.'/auth.php';

// Include module routes
require __DIR__.'/accounting.php';
require __DIR__.'/hr.php';
require __DIR__.'/performance.php';

// Module Routes
Route::middleware('auth')->prefix('modules')->name('modules.')->group(function () {
    // CRM Module
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/', [CRMController::class, 'index'])->name('index');
        Route::get('/dashboard', [CRMController::class, 'dashboard'])->name('dashboard');

        // Contacts
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/', [CRMController::class, 'contacts'])->name('index');
            Route::get('/create', [CRMController::class, 'createContact'])->name('create');

            // Bulk Upload (must be before /{contact} routes)
            Route::get('/bulk-upload', [CRMController::class, 'showBulkUpload'])->name('bulk-upload');
            Route::post('/bulk-upload', [CRMController::class, 'processBulkUpload'])->name('bulk-upload.process');
            Route::get('/download-template', [CRMController::class, 'downloadTemplate'])->name('download-template');

            Route::post('/', [CRMController::class, 'storeContact'])->name('store');
            Route::get('/{contact}', [CRMController::class, 'showContact'])->name('show');
            Route::get('/{contact}/edit', [CRMController::class, 'editContact'])->name('edit');
            Route::put('/{contact}', [CRMController::class, 'updateContact'])->name('update');
            Route::delete('/{contact}', [CRMController::class, 'destroyContact'])->name('destroy');

            // Communications
            Route::post('/{contact}/communications', [CRMController::class, 'storeCommunication'])->name('communications.store');

            // Follow-ups
            Route::post('/{contact}/follow-ups', [CRMController::class, 'storeFollowUp'])->name('follow-ups.store');
        });

        // Follow-ups
        Route::prefix('follow-ups')->name('follow-ups.')->group(function () {
            Route::get('/', [CRMController::class, 'followUpReminders'])->name('index');
            Route::patch('/{followUp}/complete', [CRMController::class, 'completeFollowUp'])->name('complete');
        });

        // Sales Funnel
        Route::get('/funnel', [CRMController::class, 'salesFunnel'])->name('funnel');
    });

    // Support Module
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('index');
        Route::get('/dashboard', [SupportController::class, 'dashboard'])->name('dashboard');

        // Tickets
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [SupportController::class, 'tickets'])->name('index');
            Route::get('/create', [SupportController::class, 'createTicket'])->name('create');
            Route::post('/', [SupportController::class, 'storeTicket'])->name('store');
            Route::get('/{ticket}', [SupportController::class, 'showTicket'])->name('show');
            Route::get('/{ticket}/edit', [SupportController::class, 'editTicket'])->name('edit');
            Route::put('/{ticket}', [SupportController::class, 'updateTicket'])->name('update');
            Route::delete('/{ticket}', [SupportController::class, 'destroyTicket'])->name('destroy');

            // Ticket Actions
            Route::patch('/{ticket}/assign', [SupportController::class, 'assignTicket'])->name('assign');
            Route::patch('/{ticket}/resolve', [SupportController::class, 'resolveTicket'])->name('resolve');
            Route::patch('/{ticket}/reopen', [SupportController::class, 'reopenTicket'])->name('reopen');
            Route::patch('/{ticket}/close', [SupportController::class, 'closeTicket'])->name('close');

            // Comments
            Route::post('/{ticket}/comments', [SupportController::class, 'storeComment'])->name('comments.store');
        });

        // Attachments
        Route::prefix('attachments')->name('attachments.')->group(function () {
            Route::get('/{attachment}/download', [SupportController::class, 'downloadAttachment'])->name('download');
            Route::delete('/{attachment}', [SupportController::class, 'destroyAttachment'])->name('destroy');
        });
    });

    // HR Module
    Route::prefix('hr')->name('hr.')->middleware('permission:hr.view')->group(function () {
        Route::get('/', [HRController::class, 'index'])->name('index');
        Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');

        // Employees
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [\App\Modules\HR\Controllers\EmployeeController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\HR\Controllers\EmployeeController::class, 'create'])->name('create')->middleware('permission:hr.employees.create');
            Route::post('/', [\App\Modules\HR\Controllers\EmployeeController::class, 'store'])->name('store')->middleware('permission:hr.employees.create');
            Route::get('/{employee}', [\App\Modules\HR\Controllers\EmployeeController::class, 'show'])->name('show');
            Route::get('/{employee}/edit', [\App\Modules\HR\Controllers\EmployeeController::class, 'edit'])->name('edit')->middleware('permission:hr.employees.edit');
            Route::put('/{employee}', [\App\Modules\HR\Controllers\EmployeeController::class, 'update'])->name('update')->middleware('permission:hr.employees.edit');
            Route::delete('/{employee}', [\App\Modules\HR\Controllers\EmployeeController::class, 'destroy'])->name('destroy')->middleware('permission:hr.employees.delete');
        });

        // Leave Requests
        Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
            Route::get('/', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'create'])->name('create')->middleware('permission:hr.leave.create');
            Route::post('/', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'store'])->name('store')->middleware('permission:hr.leave.create');
            Route::get('/{leaveRequest}', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'show'])->name('show');
            Route::get('/{leaveRequest}/edit', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'edit'])->name('edit')->middleware('permission:hr.leave.create');
            Route::put('/{leaveRequest}', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'update'])->name('update')->middleware('permission:hr.leave.create');
            Route::delete('/{leaveRequest}', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'destroy'])->name('destroy')->middleware('permission:hr.leave.manage');

            // Approval actions
            Route::post('/{leaveRequest}/approve', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'approve'])->name('approve')->middleware('permission:hr.leave.approve');
            Route::post('/{leaveRequest}/reject', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'reject'])->name('reject')->middleware('permission:hr.leave.approve');
            Route::post('/{leaveRequest}/cancel', [\App\Modules\HR\Controllers\LeaveRequestController::class, 'cancel'])->name('cancel');
        });

        // Attendance
        Route::prefix('attendance')->name('attendance.')->middleware('permission:hr.attendance.view')->group(function () {
            Route::get('/', [\App\Modules\HR\Controllers\AttendanceController::class, 'index'])->name('index');
            Route::post('/check-in', [\App\Modules\HR\Controllers\AttendanceController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [\App\Modules\HR\Controllers\AttendanceController::class, 'checkOut'])->name('check-out');
            Route::get('/reports', [\App\Modules\HR\Controllers\AttendanceController::class, 'reports'])->name('reports');
        });

        // Departments
        Route::prefix('departments')->name('departments.')->middleware('permission:hr.departments.view')->group(function () {
            Route::get('/', [\App\Modules\HR\Controllers\DepartmentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\HR\Controllers\DepartmentController::class, 'create'])->name('create')->middleware('permission:hr.departments.manage');
            Route::post('/', [\App\Modules\HR\Controllers\DepartmentController::class, 'store'])->name('store')->middleware('permission:hr.departments.manage');
            Route::get('/{department}', [\App\Modules\HR\Controllers\DepartmentController::class, 'show'])->name('show');
            Route::get('/{department}/edit', [\App\Modules\HR\Controllers\DepartmentController::class, 'edit'])->name('edit')->middleware('permission:hr.departments.manage');
            Route::put('/{department}', [\App\Modules\HR\Controllers\DepartmentController::class, 'update'])->name('update')->middleware('permission:hr.departments.manage');
            Route::delete('/{department}', [\App\Modules\HR\Controllers\DepartmentController::class, 'destroy'])->name('destroy')->middleware('permission:hr.departments.manage');
        });

        // Payroll
        Route::prefix('payroll')->name('payroll.')->middleware('permission:hr.payroll.view')->group(function () {
            Route::get('/', [\App\Modules\HR\Controllers\PayrollController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\HR\Controllers\PayrollController::class, 'create'])->name('create')->middleware('permission:hr.payroll.process');
            Route::post('/', [\App\Modules\HR\Controllers\PayrollController::class, 'store'])->name('store')->middleware('permission:hr.payroll.process');
            Route::get('/{payroll}', [\App\Modules\HR\Controllers\PayrollController::class, 'show'])->name('show');
            Route::get('/reports', [\App\Modules\HR\Controllers\PayrollController::class, 'reports'])->name('reports');

            // Approval actions
            Route::post('/{payroll}/approve', [\App\Modules\HR\Controllers\PayrollController::class, 'approve'])->name('approve')->middleware('permission:hr.payroll.approve');
            Route::post('/{payroll}/reject', [\App\Modules\HR\Controllers\PayrollController::class, 'reject'])->name('reject')->middleware('permission:hr.payroll.approve');
            Route::post('/bulk-approve', [\App\Modules\HR\Controllers\PayrollController::class, 'bulkApprove'])->name('bulk-approve')->middleware('permission:hr.payroll.approve');
            Route::post('/generate-payslips', [\App\Modules\HR\Controllers\PayrollController::class, 'generatePayslips'])->name('generate-payslips');
        });

        // Performance Reviews
        Route::prefix('performance-reviews')->name('performance-reviews.')->middleware('permission:hr.performance.view')->group(function () {
            Route::get('/', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'create'])->name('create')->middleware('permission:hr.performance.manage');
            Route::post('/', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'store'])->name('store')->middleware('permission:hr.performance.manage');
            Route::get('/{performanceReview}', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'show'])->name('show');
            Route::get('/{performanceReview}/edit', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'edit'])->name('edit')->middleware('permission:hr.performance.manage');
            Route::put('/{performanceReview}', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'update'])->name('update')->middleware('permission:hr.performance.manage');
            Route::delete('/{performanceReview}', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'destroy'])->name('destroy')->middleware('permission:hr.performance.manage');

            // Actions
            Route::post('/{performanceReview}/complete', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'complete'])->name('complete')->middleware('permission:hr.performance.manage');
            Route::post('/bulk-create', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'bulkCreate'])->name('bulk-create')->middleware('permission:hr.performance.manage');
            Route::get('/reports', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'reports'])->name('reports');
            Route::post('/export', [\App\Modules\HR\Controllers\PerformanceReviewController::class, 'export'])->name('export');
        });
    });

    // Accounting Reports
    Route::prefix('accounting/reports')->name('accounting.reports.')->middleware(['auth', 'permission:accounting.reports.view'])->group(function () {
        Route::get('/', [\App\Modules\Accounting\Controllers\ReportsController::class, 'index'])->name('index');
        Route::get('/profit-loss', [\App\Modules\Accounting\Controllers\ReportsController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/balance-sheet', [\App\Modules\Accounting\Controllers\ReportsController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/cash-flow', [\App\Modules\Accounting\Controllers\ReportsController::class, 'cashFlow'])->name('cash-flow');
        Route::get('/customers', [\App\Modules\Accounting\Controllers\ReportsController::class, 'customerReport'])->name('customers');
        Route::get('/vendors', [\App\Modules\Accounting\Controllers\ReportsController::class, 'vendorReport'])->name('vendors');
        Route::post('/export', [\App\Modules\Accounting\Controllers\ReportsController::class, 'export'])->name('export');
    });

    // Roles Module
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [\App\Modules\Roles\Controllers\RolesController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Modules\Roles\Controllers\RolesController::class, 'dashboard'])->name('dashboard');

        // Role Management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [\App\Modules\Roles\Controllers\RolesController::class, 'roles'])->name('index');
            Route::get('/create', [\App\Modules\Roles\Controllers\RolesController::class, 'createRole'])->name('create');
            Route::post('/', [\App\Modules\Roles\Controllers\RolesController::class, 'storeRole'])->name('store');
            Route::get('/{role}', [\App\Modules\Roles\Controllers\RolesController::class, 'showRole'])->name('show');
            Route::get('/{role}/edit', [\App\Modules\Roles\Controllers\RolesController::class, 'editRole'])->name('edit');
            Route::put('/{role}', [\App\Modules\Roles\Controllers\RolesController::class, 'updateRole'])->name('update');
            Route::delete('/{role}', [\App\Modules\Roles\Controllers\RolesController::class, 'destroyRole'])->name('destroy');
        });

        // User Role Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [\App\Modules\Roles\Controllers\RolesController::class, 'users'])->name('index');
            Route::get('/{user}/edit', [\App\Modules\Roles\Controllers\RolesController::class, 'editUserRoles'])->name('edit');
            Route::put('/{user}', [\App\Modules\Roles\Controllers\RolesController::class, 'updateUserRoles'])->name('update');
        });

        // Permission Management
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [\App\Modules\Roles\Controllers\RolesController::class, 'permissions'])->name('index');
        });

        // Hierarchy Management
        Route::prefix('hierarchy')->name('hierarchy.')->group(function () {
            Route::get('/', [\App\Modules\Roles\Controllers\RolesController::class, 'hierarchy'])->name('index');
        });
    });

    // Accounting Module
    Route::prefix('accounting')->name('accounting.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Modules\Accounting\Controllers\DashboardController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Modules\Accounting\Controllers\DashboardController::class, 'index'])->name('dashboard');

        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\InvoiceController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Accounting\Controllers\InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [\App\Modules\Accounting\Controllers\InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/edit', [\App\Modules\Accounting\Controllers\InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [\App\Modules\Accounting\Controllers\InvoiceController::class, 'update'])->name('update');
            Route::delete('/{invoice}', [\App\Modules\Accounting\Controllers\InvoiceController::class, 'destroy'])->name('destroy');
        });

        // Expenses
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\ExpenseController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\ExpenseController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Accounting\Controllers\ExpenseController::class, 'store'])->name('store');
            Route::get('/{expense}', [\App\Modules\Accounting\Controllers\ExpenseController::class, 'show'])->name('show');
            Route::get('/{expense}/edit', [\App\Modules\Accounting\Controllers\ExpenseController::class, 'edit'])->name('edit');
            Route::put('/{expense}', [\App\Modules\Accounting\Controllers\ExpenseController::class, 'update'])->name('update');
            Route::delete('/{expense}', [\App\Modules\Accounting\Controllers\ExpenseController::class, 'destroy'])->name('destroy');
        });

        // Customers
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\CustomerController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\CustomerController::class, 'create'])->name('create');

            // Bulk upload routes (must be before parameterized routes)
            Route::get('/bulk-upload', [\App\Modules\Accounting\Controllers\CustomerController::class, 'bulkUpload'])->name('bulk-upload');
            Route::post('/bulk-upload', [\App\Modules\Accounting\Controllers\CustomerController::class, 'processBulkUpload'])->name('process-bulk-upload');
            Route::get('/download-template', [\App\Modules\Accounting\Controllers\CustomerController::class, 'downloadTemplate'])->name('download-template');

            Route::post('/', [\App\Modules\Accounting\Controllers\CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}', [\App\Modules\Accounting\Controllers\CustomerController::class, 'show'])->name('show');
            Route::get('/{customer}/edit', [\App\Modules\Accounting\Controllers\CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [\App\Modules\Accounting\Controllers\CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [\App\Modules\Accounting\Controllers\CustomerController::class, 'destroy'])->name('destroy');
        });

        // Vendors
        Route::prefix('vendors')->name('vendors.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\VendorController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\VendorController::class, 'create'])->name('create');

            // Bulk upload routes (must be before parameterized routes)
            Route::get('/bulk-upload', [\App\Modules\Accounting\Controllers\VendorController::class, 'bulkUpload'])->name('bulk-upload');
            Route::post('/bulk-upload', [\App\Modules\Accounting\Controllers\VendorController::class, 'processBulkUpload'])->name('process-bulk-upload');
            Route::get('/download-template', [\App\Modules\Accounting\Controllers\VendorController::class, 'downloadTemplate'])->name('download-template');

            Route::post('/', [\App\Modules\Accounting\Controllers\VendorController::class, 'store'])->name('store');
            Route::get('/{vendor}', [\App\Modules\Accounting\Controllers\VendorController::class, 'show'])->name('show');
            Route::get('/{vendor}/edit', [\App\Modules\Accounting\Controllers\VendorController::class, 'edit'])->name('edit');
            Route::put('/{vendor}', [\App\Modules\Accounting\Controllers\VendorController::class, 'update'])->name('update');
            Route::delete('/{vendor}', [\App\Modules\Accounting\Controllers\VendorController::class, 'destroy'])->name('destroy');
        });

        // Recurring Payments
        Route::prefix('recurring')->name('recurring.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\RecurringController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\RecurringController::class, 'create'])->name('create')->middleware('permission:accounting.entries.create');
            Route::post('/', [\App\Modules\Accounting\Controllers\RecurringController::class, 'store'])->name('store')->middleware('permission:accounting.entries.create');
            Route::get('/{profile}', [\App\Modules\Accounting\Controllers\RecurringController::class, 'show'])->name('show');
            Route::get('/{profile}/edit', [\App\Modules\Accounting\Controllers\RecurringController::class, 'edit'])->name('edit')->middleware('permission:accounting.entries.edit');
            Route::put('/{profile}', [\App\Modules\Accounting\Controllers\RecurringController::class, 'update'])->name('update')->middleware('permission:accounting.entries.edit');
            Route::delete('/{profile}', [\App\Modules\Accounting\Controllers\RecurringController::class, 'destroy'])->name('destroy')->middleware('permission:accounting.entries.delete');

            // Actions
            Route::post('/{profile}/pause', [\App\Modules\Accounting\Controllers\RecurringController::class, 'pause'])->name('pause')->middleware('permission:accounting.entries.edit');
            Route::post('/{profile}/resume', [\App\Modules\Accounting\Controllers\RecurringController::class, 'resume'])->name('resume')->middleware('permission:accounting.entries.edit');
            Route::post('/{profile}/process-now', [\App\Modules\Accounting\Controllers\RecurringController::class, 'processNow'])->name('process-now')->middleware('permission:accounting.entries.create');
            Route::post('/process-due', [\App\Modules\Accounting\Controllers\RecurringController::class, 'processDue'])->name('process-due')->middleware('permission:accounting.entries.create');
            Route::get('/dashboard-data', [\App\Modules\Accounting\Controllers\RecurringController::class, 'getDashboardData'])->name('dashboard-data');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\ReportsController::class, 'index'])->name('index');
            Route::get('/profit-loss', [\App\Modules\Accounting\Controllers\ReportsController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/balance-sheet', [\App\Modules\Accounting\Controllers\ReportsController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('/cash-flow', [\App\Modules\Accounting\Controllers\ReportsController::class, 'cashFlow'])->name('cash-flow');
            Route::get('/customers', [\App\Modules\Accounting\Controllers\ReportsController::class, 'customerReport'])->name('customers');
            Route::get('/vendors', [\App\Modules\Accounting\Controllers\ReportsController::class, 'vendorReport'])->name('vendors');
            Route::post('/export', [\App\Modules\Accounting\Controllers\ReportsController::class, 'export'])->name('export');
        });

        // Currencies
        Route::prefix('currencies')->name('currencies.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'store'])->name('store');
            Route::get('/{currency}', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'show'])->name('show');
            Route::get('/{currency}/edit', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'edit'])->name('edit');
            Route::put('/{currency}', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'update'])->name('update');
            Route::delete('/{currency}', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'destroy'])->name('destroy');

            // Actions
            Route::post('/update-exchange-rates', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'updateExchangeRates'])->name('update-exchange-rates');
            Route::post('/{currency}/set-base', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'setBaseCurrency'])->name('set-base');
            Route::post('/{currency}/toggle-status', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/convert', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'convert'])->name('convert');
            Route::get('/export/rates', [\App\Modules\Accounting\Controllers\CurrencyController::class, 'exportRates'])->name('export-rates');
        });

        // Taxes
        Route::prefix('taxes')->name('taxes.')->middleware('permission:accounting.settings.manage')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\TaxController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\TaxController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Accounting\Controllers\TaxController::class, 'store'])->name('store');
            Route::get('/{tax}', [\App\Modules\Accounting\Controllers\TaxController::class, 'show'])->name('show');
            Route::get('/{tax}/edit', [\App\Modules\Accounting\Controllers\TaxController::class, 'edit'])->name('edit');
            Route::put('/{tax}', [\App\Modules\Accounting\Controllers\TaxController::class, 'update'])->name('update');
            Route::delete('/{tax}', [\App\Modules\Accounting\Controllers\TaxController::class, 'destroy'])->name('destroy');

            // Actions
            Route::post('/{tax}/set-default', [\App\Modules\Accounting\Controllers\TaxController::class, 'setDefault'])->name('set-default');
            Route::post('/{tax}/toggle-status', [\App\Modules\Accounting\Controllers\TaxController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/calculate', [\App\Modules\Accounting\Controllers\TaxController::class, 'calculate'])->name('calculate');
            Route::get('/by-country', [\App\Modules\Accounting\Controllers\TaxController::class, 'getByCountry'])->name('by-country');
            Route::post('/{tax}/duplicate', [\App\Modules\Accounting\Controllers\TaxController::class, 'duplicate'])->name('duplicate');
            Route::get('/export', [\App\Modules\Accounting\Controllers\TaxController::class, 'export'])->name('export');
        });

        // Chart of Accounts
        Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
            Route::get('/', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'store'])->name('store');
            Route::get('/{account}', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'show'])->name('show');
            Route::get('/{account}/edit', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'edit'])->name('edit');
            Route::put('/{account}', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'update'])->name('update');
            Route::delete('/{account}', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'destroy'])->name('destroy');
            Route::get('/{account}/transactions', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'transactions'])->name('transactions');
            Route::get('/export', [\App\Modules\Accounting\Controllers\ChartOfAccountController::class, 'export'])->name('export');
        });
    });
});

// Admin Routes - Role-Based Access Control
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');
    // Role Management
    Route::middleware('role:top_management|middle_management')->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::prefix('roles/{role}')->name('roles.')->group(function () {
            Route::post('assign-user', [\App\Http\Controllers\Admin\RoleController::class, 'assignUser'])->name('assign-user');
            Route::delete('remove-user', [\App\Http\Controllers\Admin\RoleController::class, 'removeUser'])->name('remove-user');
            Route::get('users', [\App\Http\Controllers\Admin\RoleController::class, 'getUsers'])->name('users');
            Route::patch('permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('permissions');
            Route::post('clone', [\App\Http\Controllers\Admin\RoleController::class, 'clone'])->name('clone');
        });

        Route::get('permission-matrix', [\App\Http\Controllers\Admin\RoleController::class, 'permissionMatrix'])->name('roles.permission-matrix');
        Route::patch('bulk-permissions', [\App\Http\Controllers\Admin\RoleController::class, 'bulkUpdatePermissions'])->name('roles.bulk-permissions');

        // User Role Management
        Route::resource('user-roles', \App\Http\Controllers\Admin\UserRoleController::class)->only(['index', 'show']);
        Route::prefix('user-roles/{user}')->name('user-roles.')->group(function () {
            Route::put('update', [\App\Http\Controllers\Admin\UserRoleController::class, 'update'])->name('update');
            Route::post('assign-role', [\App\Http\Controllers\Admin\UserRoleController::class, 'assignRole'])->name('assign-role');
            Route::delete('remove-role', [\App\Http\Controllers\Admin\UserRoleController::class, 'removeRole'])->name('remove-role');
            Route::get('permissions', [\App\Http\Controllers\Admin\UserRoleController::class, 'permissions'])->name('permissions');
        });

        Route::post('bulk-assign-roles', [\App\Http\Controllers\Admin\UserRoleController::class, 'bulkAssign'])->name('user-roles.bulk-assign');
        Route::delete('bulk-remove-roles', [\App\Http\Controllers\Admin\UserRoleController::class, 'bulkRemove'])->name('user-roles.bulk-remove');
        Route::get('search-users', [\App\Http\Controllers\Admin\UserRoleController::class, 'searchUsers'])->name('user-roles.search-users');
        Route::get('role-hierarchy', [\App\Http\Controllers\Admin\UserRoleController::class, 'hierarchy'])->name('user-roles.hierarchy');
        Route::get('export-user-roles', [\App\Http\Controllers\Admin\UserRoleController::class, 'export'])->name('user-roles.export');

        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // WhatsApp Configuration
        Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WhatsAppController::class, 'index'])->name('index');
            Route::put('/update', [\App\Http\Controllers\Admin\WhatsAppController::class, 'update'])->name('update');
            Route::post('/test', [\App\Http\Controllers\Admin\WhatsAppController::class, 'test'])->name('test');
            Route::get('/profile', [\App\Http\Controllers\Admin\WhatsAppController::class, 'profile'])->name('profile');
        });
    });
});
