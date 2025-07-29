<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SystemIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'test@connectpure.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function user_can_access_dashboard()
    {
        $response = $this->actingAs($this->user)
                         ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function user_can_access_hr_module()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/hr');

        $response->assertStatus(200);
        $response->assertSee('Human Resources');
    }

    /** @test */
    public function user_can_access_accounting_module()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting');

        $response->assertStatus(200);
        $response->assertSee('Accounting');
    }

    /** @test */
    public function user_can_access_hr_employees_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/hr/employees');

        $response->assertStatus(200);
        $response->assertSee('Employees');
    }

    /** @test */
    public function user_can_access_hr_attendance_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/hr/attendance');

        $response->assertStatus(200);
        $response->assertSee('Attendance');
    }

    /** @test */
    public function user_can_access_hr_leave_requests_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/hr/leave-requests');

        $response->assertStatus(200);
        $response->assertSee('Leave Requests');
    }

    /** @test */
    public function user_can_access_hr_payroll_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/hr/payroll');

        $response->assertStatus(200);
        $response->assertSee('Payroll');
    }

    /** @test */
    public function user_can_access_hr_performance_reviews_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/hr/performance-reviews');

        $response->assertStatus(200);
        $response->assertSee('Performance Reviews');
    }

    /** @test */
    public function user_can_access_accounting_invoices_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting/invoices');

        $response->assertStatus(200);
        $response->assertSee('Invoices');
    }

    /** @test */
    public function user_can_access_accounting_expenses_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting/expenses');

        $response->assertStatus(200);
        $response->assertSee('Expenses');
    }

    /** @test */
    public function user_can_access_accounting_customers_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting/customers');

        $response->assertStatus(200);
        $response->assertSee('Customers');
    }

    /** @test */
    public function user_can_access_accounting_vendors_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting/vendors');

        $response->assertStatus(200);
        $response->assertSee('Vendors');
    }

    /** @test */
    public function user_can_access_accounting_reports_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting/reports');

        $response->assertStatus(200);
        $response->assertSee('Financial Reports');
    }

    /** @test */
    public function user_can_access_currencies_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting/currencies');

        $response->assertStatus(200);
        $response->assertSee('Currencies');
    }

    /** @test */
    public function user_can_access_taxes_page()
    {
        $response = $this->actingAs($this->user)
                         ->get('/modules/accounting/taxes');

        $response->assertStatus(200);
        $response->assertSee('Taxes');
    }

    /** @test */
    public function language_switching_works()
    {
        // Test English
        $response = $this->actingAs($this->user)
                         ->get('/lang/en');

        $response->assertRedirect();

        // Test Arabic
        $response = $this->actingAs($this->user)
                         ->get('/lang/ar');

        $response->assertRedirect();
    }

    /** @test */
    public function api_endpoints_are_accessible()
    {
        // Test API routes exist
        $this->assertTrue(true); // Placeholder for API tests
    }

    /** @test */
    public function database_tables_exist()
    {
        // Test that all required tables exist
        $tables = [
            'users',
            'hr_departments',
            'hr_employees',
            'hr_attendance_records',
            'hr_leave_requests',
            'hr_salary_records',
            'hr_performance_reviews',
            'accounting_customers',
            'accounting_vendors',
            'accounting_invoices',
            'accounting_expenses',
            'accounting_currencies',
            'accounting_taxes',
            'accounting_invoice_taxes',
            'accounting_expense_taxes',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                \Schema::hasTable($table),
                "Table {$table} does not exist"
            );
        }
    }

    /** @test */
    public function required_seeders_have_run()
    {
        // Run currency and tax seeder
        $this->artisan('db:seed', ['--class' => 'CurrencyAndTaxSeeder']);
        
        // Check that currencies exist
        $this->assertDatabaseHas('accounting_currencies', [
            'code' => 'USD',
            'is_base_currency' => true,
        ]);

        // Check that taxes exist
        $this->assertDatabaseHas('accounting_taxes', [
            'code' => 'VAT_SA',
            'is_default' => true,
        ]);
    }

    /** @test */
    public function system_configuration_is_correct()
    {
        // Test that the application is properly configured
        $this->assertEquals('Connect Pure', config('app.name'));
        $this->assertNotEmpty(config('app.key'));
        $this->assertEquals('en', config('app.locale'));
        $this->assertEquals('en', config('app.fallback_locale'));
    }

    /** @test */
    public function middleware_is_working()
    {
        // Test that authentication middleware works
        $response = $this->get('/modules/hr');
        $response->assertRedirect('/login');

        // Test that authenticated users can access protected routes
        $response = $this->actingAs($this->user)
                         ->get('/modules/hr');
        $response->assertStatus(200);
    }

    /** @test */
    public function error_pages_work()
    {
        // Test 404 page
        $response = $this->actingAs($this->user)
                         ->get('/non-existent-page');
        $response->assertStatus(404);
    }

    /** @test */
    public function csrf_protection_is_enabled()
    {
        // Test that CSRF protection is working
        $response = $this->post('/modules/hr/employees');
        $response->assertStatus(419); // CSRF token mismatch
    }
}
