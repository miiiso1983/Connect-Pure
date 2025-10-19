<?php

namespace App\Providers;

use App\Models\User;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\HR\Models\Employee;
use App\Policies\EmployeePolicy;
use App\Policies\InvoicePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
    }
}
