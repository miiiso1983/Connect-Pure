<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicSystemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function application_returns_successful_response()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    /** @test */
    public function register_page_loads()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Register');
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function language_switching_routes_exist()
    {
        $user = User::factory()->create();

        // Test English route
        $response = $this->actingAs($user)->get('/lang/en');
        $response->assertRedirect();

        // Test Arabic route
        $response = $this->actingAs($user)->get('/lang/ar');
        $response->assertRedirect();
    }

    /** @test */
    public function hr_module_routes_exist()
    {
        $user = User::factory()->create();

        $routes = [
            '/modules/hr',
            '/modules/hr/employees',
            '/modules/hr/attendance',
            '/modules/hr/leave-requests',
            '/modules/hr/payroll',
            '/modules/hr/performance-reviews',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $this->assertContains($response->status(), [200, 302], "Route {$route} failed");
        }
    }

    /** @test */
    public function accounting_module_routes_exist()
    {
        $user = User::factory()->create();

        $routes = [
            '/modules/accounting',
            '/modules/accounting/invoices',
            '/modules/accounting/expenses',
            '/modules/accounting/customers',
            '/modules/accounting/vendors',
            '/modules/accounting/reports',
            '/modules/accounting/currencies',
            '/modules/accounting/taxes',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $this->assertContains($response->status(), [200, 302], "Route {$route} failed");
        }
    }

    /** @test */
    public function system_configuration_is_correct()
    {
        $this->assertEquals('Connect Pure', config('app.name'));
        $this->assertNotEmpty(config('app.key'));
        $this->assertEquals('en', config('app.locale'));
        $this->assertEquals('en', config('app.fallback_locale'));
    }

    /** @test */
    public function csrf_protection_is_enabled()
    {
        $response = $this->post('/modules/hr/employees');
        $this->assertContains($response->status(), [419, 302], 'CSRF protection should be enabled');
    }

    /** @test */
    public function error_pages_work()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/non-existent-page');
        $response->assertStatus(404);
    }

    /** @test */
    public function middleware_protects_routes()
    {
        // Test that protected routes redirect unauthenticated users
        $protectedRoutes = [
            '/modules/hr',
            '/modules/accounting',
            '/dashboard',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function application_environment_is_testing()
    {
        $this->assertEquals('testing', app()->environment());
    }

    /** @test */
    public function database_connection_works()
    {
        $this->assertDatabaseCount('users', 0);

        User::factory()->create();

        $this->assertDatabaseCount('users', 1);
    }

    /** @test */
    public function user_factory_works()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNotNull($user->password);
    }

    /** @test */
    public function session_works()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);

        // Test session flash messages
        session()->flash('success', 'Test message');
        $this->assertEquals('Test message', session('success'));
    }

    /** @test */
    public function validation_works()
    {
        $user = User::factory()->create();

        // Test validation by submitting invalid data
        $response = $this->actingAs($user)->post('/modules/hr/employees', [
            '_token' => csrf_token(),
            'email' => 'invalid-email',
        ]);

        // Should return validation errors or redirect
        $this->assertContains($response->status(), [422, 302]);
    }

    /** @test */
    public function localization_files_exist()
    {
        $this->assertFileExists(lang_path('en'));
        $this->assertFileExists(lang_path('ar'));

        // Check for specific language files
        $this->assertFileExists(lang_path('en/auth.php'));
        $this->assertFileExists(lang_path('ar/auth.php'));
    }

    /** @test */
    public function views_compile_without_errors()
    {
        $user = User::factory()->create();

        // Test that main views compile
        $views = [
            '/dashboard',
            '/modules/hr',
            '/modules/accounting',
        ];

        foreach ($views as $view) {
            $response = $this->actingAs($user)->get($view);
            $this->assertContains($response->status(), [200, 302], "View {$view} failed to compile");
        }
    }
}
