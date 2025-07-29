<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use App\Modules\Accounting\Services\DashboardService;
use App\Modules\Accounting\Services\ReportService;

class AccountingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/accounting.php', 'accounting'
        );

        // Register services
        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService();
        });

        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });

        // Register aliases
        $this->app->alias(DashboardService::class, 'accounting.dashboard');
        $this->app->alias(ReportService::class, 'accounting.reports');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/accounting.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views/modules/accounting', 'accounting');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'accounting');

        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/accounting.php' => config_path('accounting.php'),
            ], 'accounting-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'accounting-migrations');

            // Publish views
            $this->publishes([
                __DIR__.'/../../resources/views/modules/accounting' => resource_path('views/modules/accounting'),
            ], 'accounting-views');

            // Publish translations
            $this->publishes([
                __DIR__.'/../../lang' => resource_path('lang'),
            ], 'accounting-translations');

            // Publish assets
            $this->publishes([
                __DIR__.'/../../public/css/accounting.css' => public_path('css/accounting.css'),
            ], 'accounting-assets');
        }

        // Register view composers
        $this->registerViewComposers();

        // Register gates and policies
        $this->registerGates();

        // Register event listeners
        $this->registerEventListeners();

        // Register custom validation rules
        $this->registerValidationRules();

        // Register blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Register view composers
     */
    protected function registerViewComposers(): void
    {
        // Share accounting configuration with all accounting views
        View::composer('accounting::*', function ($view) {
            $view->with('accountingConfig', config('accounting'));
        });

        // Share currency list with relevant views
        View::composer([
            'accounting::invoices.*',
            'accounting::customers.*',
            'accounting::vendors.*',
            'accounting::expenses.*'
        ], function ($view) {
            $view->with('currencies', config('accounting.currencies.symbols'));
        });

        // Share payment methods with payment-related views
        View::composer([
            'accounting::payments.*',
            'accounting::invoices.*',
            'accounting::expenses.*'
        ], function ($view) {
            $view->with('paymentMethods', config('accounting.payments.methods'));
        });

        // Share tax rates with transaction views
        View::composer([
            'accounting::invoices.*',
            'accounting::products.*',
            'accounting::expenses.*'
        ], function ($view) {
            $view->with('defaultTaxRates', config('accounting.taxes.default_rates'));
        });
    }

    /**
     * Register authorization gates
     */
    protected function registerGates(): void
    {
        // Accounting module access
        Gate::define('access-accounting', function ($user) {
            return $user->hasPermission('accounting.access');
        });

        // Invoice permissions
        Gate::define('create-invoices', function ($user) {
            return $user->hasPermission('accounting.invoices.create');
        });

        Gate::define('edit-invoices', function ($user) {
            return $user->hasPermission('accounting.invoices.edit');
        });

        Gate::define('delete-invoices', function ($user) {
            return $user->hasPermission('accounting.invoices.delete');
        });

        Gate::define('send-invoices', function ($user) {
            return $user->hasPermission('accounting.invoices.send');
        });

        // Expense permissions
        Gate::define('create-expenses', function ($user) {
            return $user->hasPermission('accounting.expenses.create');
        });

        Gate::define('approve-expenses', function ($user) {
            return $user->hasPermission('accounting.expenses.approve');
        });

        Gate::define('view-all-expenses', function ($user) {
            return $user->hasPermission('accounting.expenses.view_all');
        });

        // Customer permissions
        Gate::define('manage-customers', function ($user) {
            return $user->hasPermission('accounting.customers.manage');
        });

        // Vendor permissions
        Gate::define('manage-vendors', function ($user) {
            return $user->hasPermission('accounting.vendors.manage');
        });

        // Product permissions
        Gate::define('manage-products', function ($user) {
            return $user->hasPermission('accounting.products.manage');
        });

        // Report permissions
        Gate::define('view-financial-reports', function ($user) {
            return $user->hasPermission('accounting.reports.financial');
        });

        Gate::define('view-management-reports', function ($user) {
            return $user->hasPermission('accounting.reports.management');
        });

        // Chart of accounts permissions
        Gate::define('manage-chart-of-accounts', function ($user) {
            return $user->hasPermission('accounting.chart_of_accounts.manage');
        });

        // Payroll permissions
        Gate::define('manage-payroll', function ($user) {
            return $user->hasPermission('accounting.payroll.manage');
        });

        Gate::define('process-payroll', function ($user) {
            return $user->hasPermission('accounting.payroll.process');
        });

        // Settings permissions
        Gate::define('manage-accounting-settings', function ($user) {
            return $user->hasPermission('accounting.settings.manage');
        });
    }

    /**
     * Register event listeners
     */
    protected function registerEventListeners(): void
    {
        // Invoice events
        $this->app['events']->listen(
            'App\Modules\Accounting\Events\InvoiceCreated',
            'App\Modules\Accounting\Listeners\SendInvoiceNotification'
        );

        $this->app['events']->listen(
            'App\Modules\Accounting\Events\InvoicePaid',
            'App\Modules\Accounting\Listeners\UpdateCustomerBalance'
        );

        $this->app['events']->listen(
            'App\Modules\Accounting\Events\InvoiceOverdue',
            'App\Modules\Accounting\Listeners\SendOverdueNotification'
        );

        // Expense events
        $this->app['events']->listen(
            'App\Modules\Accounting\Events\ExpenseSubmitted',
            'App\Modules\Accounting\Listeners\NotifyApprover'
        );

        $this->app['events']->listen(
            'App\Modules\Accounting\Events\ExpenseApproved',
            'App\Modules\Accounting\Listeners\NotifySubmitter'
        );

        // Payment events
        $this->app['events']->listen(
            'App\Modules\Accounting\Events\PaymentReceived',
            'App\Modules\Accounting\Listeners\ApplyPaymentToInvoices'
        );

        // Product events
        $this->app['events']->listen(
            'App\Modules\Accounting\Events\ProductStockLow',
            'App\Modules\Accounting\Listeners\SendLowStockAlert'
        );
    }

    /**
     * Register custom validation rules
     */
    protected function registerValidationRules(): void
    {
        // Validate account code format
        \Validator::extend('account_code', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[1-9]\d{3}$/', $value);
        });

        // Validate currency code
        \Validator::extend('currency_code', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, config('accounting.currencies.enabled'));
        });

        // Validate tax rate
        \Validator::extend('tax_rate', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) && $value >= 0 && $value <= 100;
        });

        // Validate payment method
        \Validator::extend('payment_method', function ($attribute, $value, $parameters, $validator) {
            return array_key_exists($value, config('accounting.payments.methods'));
        });

        // Validate invoice number format
        \Validator::extend('invoice_number', function ($attribute, $value, $parameters, $validator) {
            $prefix = config('accounting.defaults.invoice_prefix');
            return str_starts_with($value, $prefix);
        });

        // Validate expense amount
        \Validator::extend('expense_amount', function ($attribute, $value, $parameters, $validator) {
            $limit = config('accounting.expenses.approval_limit');
            return is_numeric($value) && $value > 0 && $value <= $limit * 10; // Max 10x approval limit
        });
    }

    /**
     * Register custom Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // Format currency
        \Blade::directive('currency', function ($expression) {
            return "<?php echo number_format($expression, 2); ?>";
        });

        // Format percentage
        \Blade::directive('percentage', function ($expression) {
            return "<?php echo number_format($expression, 2) . '%'; ?>";
        });

        // Check accounting permission
        \Blade::directive('canAccounting', function ($expression) {
            return "<?php if(Gate::allows($expression)): ?>";
        });

        \Blade::directive('endcanAccounting', function () {
            return "<?php endif; ?>";
        });

        // Display invoice status badge
        \Blade::directive('invoiceStatus', function ($expression) {
            return "<?php echo view('accounting::components.invoice-status', ['status' => $expression]); ?>";
        });

        // Display expense status badge
        \Blade::directive('expenseStatus', function ($expression) {
            return "<?php echo view('accounting::components.expense-status', ['status' => $expression]); ?>";
        });

        // Format account number
        \Blade::directive('accountNumber', function ($expression) {
            return "<?php echo substr($expression, 0, 1) . '-' . substr($expression, 1); ?>";
        });

        // RTL support for accounting module
        \Blade::directive('accountingRtl', function () {
            return "<?php echo in_array(app()->getLocale(), config('accounting.localization.rtl_locales')) ? 'dir=\"rtl\"' : ''; ?>";
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            DashboardService::class,
            ReportService::class,
            'accounting.dashboard',
            'accounting.reports',
        ];
    }
}
