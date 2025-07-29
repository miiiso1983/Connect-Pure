<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Accounting\Controllers\DashboardController;
use App\Modules\Accounting\Controllers\InvoiceController;
use App\Modules\Accounting\Controllers\CustomerController;
use App\Modules\Accounting\Controllers\VendorController;
use App\Modules\Accounting\Controllers\ExpenseController;
use App\Modules\Accounting\Controllers\ProductController;
use App\Modules\Accounting\Controllers\PaymentController;
use App\Modules\Accounting\Controllers\ReportController;
use App\Modules\Accounting\Controllers\ChartOfAccountController;
use App\Modules\Accounting\Controllers\PayrollController;
use App\Modules\Accounting\Controllers\RecurringController;
use App\Modules\Accounting\Controllers\TaxRateController;
use App\Modules\Accounting\Controllers\JournalEntryController;

/*
|--------------------------------------------------------------------------
| Accounting Module Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the accounting module including invoices,
| customers, vendors, expenses, reports, and other accounting features.
|
*/

Route::prefix('modules/accounting')->name('modules.accounting.')->middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');
    Route::get('/chart-data', [DashboardController::class, 'getChartData'])->name('chart.data');
    Route::get('/quick-stats', [DashboardController::class, 'getQuickStats'])->name('quick.stats');
    Route::get('/financial-summary', [DashboardController::class, 'getFinancialSummary'])->name('financial.summary');
    Route::get('/search', [DashboardController::class, 'search'])->name('search');

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::post('/{invoice}/send', [InvoiceController::class, 'send'])->name('send');
        Route::post('/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('duplicate');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPDF'])->name('pdf');
        Route::post('/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-paid');
        Route::post('/{invoice}/mark-viewed', [InvoiceController::class, 'markAsViewed'])->name('mark-viewed');
        
        // Invoice Items
        Route::post('/{invoice}/items', [InvoiceController::class, 'addItem'])->name('items.store');
        Route::put('/{invoice}/items/{item}', [InvoiceController::class, 'updateItem'])->name('items.update');
        Route::delete('/{invoice}/items/{item}', [InvoiceController::class, 'removeItem'])->name('items.destroy');
    });

    // Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{customer}/invoices', [CustomerController::class, 'invoices'])->name('invoices');
        Route::get('/{customer}/payments', [CustomerController::class, 'payments'])->name('payments');
        Route::get('/{customer}/statements', [CustomerController::class, 'statements'])->name('statements');
    });

    // Vendors
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/create', [VendorController::class, 'create'])->name('create');
        Route::post('/', [VendorController::class, 'store'])->name('store');
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::get('/{vendor}/edit', [VendorController::class, 'edit'])->name('edit');
        Route::put('/{vendor}', [VendorController::class, 'update'])->name('update');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');
        Route::post('/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Products & Services
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
        Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('low-stock');
    });

    // Expenses
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::post('/{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
        Route::post('/{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
        Route::post('/{expense}/mark-paid', [ExpenseController::class, 'markAsPaid'])->name('mark-paid');
        Route::post('/bulk-approve', [ExpenseController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ExpenseController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
        Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
        Route::post('/{payment}/void', [PaymentController::class, 'void'])->name('void');
    });

    // Chart of Accounts
    Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
        Route::get('/', [ChartOfAccountController::class, 'index'])->name('index');
        Route::get('/create', [ChartOfAccountController::class, 'create'])->name('create');
        Route::post('/', [ChartOfAccountController::class, 'store'])->name('store');
        Route::get('/{account}', [ChartOfAccountController::class, 'show'])->name('show');
        Route::get('/{account}/edit', [ChartOfAccountController::class, 'edit'])->name('edit');
        Route::put('/{account}', [ChartOfAccountController::class, 'update'])->name('update');
        Route::delete('/{account}', [ChartOfAccountController::class, 'destroy'])->name('destroy');
        Route::get('/{account}/transactions', [ChartOfAccountController::class, 'transactions'])->name('transactions');
    });

    // Journal Entries
    Route::prefix('journal-entries')->name('journal-entries.')->group(function () {
        Route::get('/', [JournalEntryController::class, 'index'])->name('index');
        Route::get('/create', [JournalEntryController::class, 'create'])->name('create');
        Route::post('/', [JournalEntryController::class, 'store'])->name('store');
        Route::get('/{journalEntry}', [JournalEntryController::class, 'show'])->name('show');
        Route::get('/{journalEntry}/edit', [JournalEntryController::class, 'edit'])->name('edit');
        Route::put('/{journalEntry}', [JournalEntryController::class, 'update'])->name('update');
        Route::delete('/{journalEntry}', [JournalEntryController::class, 'destroy'])->name('destroy');
        Route::post('/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('post');
        Route::post('/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])->name('reverse');
    });

    // Tax Rates
    Route::prefix('tax-rates')->name('tax-rates.')->group(function () {
        Route::get('/', [TaxRateController::class, 'index'])->name('index');
        Route::get('/create', [TaxRateController::class, 'create'])->name('create');
        Route::post('/', [TaxRateController::class, 'store'])->name('store');
        Route::get('/{taxRate}', [TaxRateController::class, 'show'])->name('show');
        Route::get('/{taxRate}/edit', [TaxRateController::class, 'edit'])->name('edit');
        Route::put('/{taxRate}', [TaxRateController::class, 'update'])->name('update');
        Route::delete('/{taxRate}', [TaxRateController::class, 'destroy'])->name('destroy');
    });

    // Recurring Transactions
    Route::prefix('recurring')->name('recurring.')->group(function () {
        Route::get('/', [RecurringController::class, 'index'])->name('index');
        Route::get('/create', [RecurringController::class, 'create'])->name('create');
        Route::post('/', [RecurringController::class, 'store'])->name('store');
        Route::get('/{recurring}', [RecurringController::class, 'show'])->name('show');
        Route::get('/{recurring}/edit', [RecurringController::class, 'edit'])->name('edit');
        Route::put('/{recurring}', [RecurringController::class, 'update'])->name('update');
        Route::delete('/{recurring}', [RecurringController::class, 'destroy'])->name('destroy');
        Route::post('/{recurring}/pause', [RecurringController::class, 'pause'])->name('pause');
        Route::post('/{recurring}/resume', [RecurringController::class, 'resume'])->name('resume');
        Route::post('/{recurring}/run-now', [RecurringController::class, 'runNow'])->name('run-now');
    });

    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/create', [PayrollController::class, 'create'])->name('create');
        Route::post('/', [PayrollController::class, 'store'])->name('store');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::get('/{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
        Route::put('/{payroll}', [PayrollController::class, 'update'])->name('update');
        Route::delete('/{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
        Route::post('/{payroll}/calculate', [PayrollController::class, 'calculate'])->name('calculate');
        Route::post('/{payroll}/approve', [PayrollController::class, 'approve'])->name('approve');
        Route::post('/{payroll}/process', [PayrollController::class, 'process'])->name('process');
        
        // Employees
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [PayrollController::class, 'employees'])->name('index');
            Route::get('/create', [PayrollController::class, 'createEmployee'])->name('create');
            Route::post('/', [PayrollController::class, 'storeEmployee'])->name('store');
            Route::get('/{employee}', [PayrollController::class, 'showEmployee'])->name('show');
            Route::get('/{employee}/edit', [PayrollController::class, 'editEmployee'])->name('edit');
            Route::put('/{employee}', [PayrollController::class, 'updateEmployee'])->name('update');
            Route::delete('/{employee}', [PayrollController::class, 'destroyEmployee'])->name('destroy');
        });
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        
        // Financial Reports
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash-flow');
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/general-ledger', [ReportController::class, 'generalLedger'])->name('general-ledger');
        
        // Customer Reports
        Route::get('/customer-aging', [ReportController::class, 'customerAging'])->name('customer-aging');
        Route::get('/customer-statements', [ReportController::class, 'customerStatements'])->name('customer-statements');
        Route::get('/sales-by-customer', [ReportController::class, 'salesByCustomer'])->name('sales-by-customer');
        
        // Vendor Reports
        Route::get('/vendor-aging', [ReportController::class, 'vendorAging'])->name('vendor-aging');
        Route::get('/expenses-by-vendor', [ReportController::class, 'expensesByVendor'])->name('expenses-by-vendor');
        
        // Product Reports
        Route::get('/inventory-valuation', [ReportController::class, 'inventoryValuation'])->name('inventory-valuation');
        Route::get('/sales-by-product', [ReportController::class, 'salesByProduct'])->name('sales-by-product');
        
        // Tax Reports
        Route::get('/sales-tax', [ReportController::class, 'salesTax'])->name('sales-tax');
        Route::get('/purchase-tax', [ReportController::class, 'purchaseTax'])->name('purchase-tax');
        
        // Export Routes
        Route::post('/export/{type}', [ReportController::class, 'export'])->name('export');
        Route::get('/download/{file}', [ReportController::class, 'download'])->name('download');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [DashboardController::class, 'settings'])->name('index');
        Route::post('/update', [DashboardController::class, 'updateSettings'])->name('update');
        Route::post('/backup', [DashboardController::class, 'backup'])->name('backup');
        Route::post('/restore', [DashboardController::class, 'restore'])->name('restore');
    });

    // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/vendors/search', [VendorController::class, 'search'])->name('vendors.search');
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/accounts/search', [ChartOfAccountController::class, 'search'])->name('accounts.search');
        Route::get('/invoice/{invoice}/items', [InvoiceController::class, 'getItems'])->name('invoice.items');
        Route::get('/customer/{customer}/balance', [CustomerController::class, 'getBalance'])->name('customer.balance');
        Route::get('/product/{product}/price', [ProductController::class, 'getPrice'])->name('product.price');
        Route::get('/tax-rates/by-location', [TaxRateController::class, 'getByLocation'])->name('tax-rates.by-location');
        Route::get('/currency/rates', [DashboardController::class, 'getCurrencyRates'])->name('currency.rates');
    });
});
