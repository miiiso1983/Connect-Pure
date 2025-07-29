<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Role-based redirection
        $user = Auth::user();
        $intendedUrl = $this->getRedirectUrlBasedOnRole($user);

        return redirect()->intended($intendedUrl);
    }

    /**
     * Get redirect URL based on user's role.
     */
    private function getRedirectUrlBasedOnRole($user): string
    {
        // Master admin gets full access
        if ($user->hasRole('master-admin')) {
            return route('dashboard');
        }

        // Get user's primary role (first role)
        $primaryRole = $user->roles->first();

        if (!$primaryRole) {
            return route('dashboard');
        }

        // Role-based redirection logic
        switch ($primaryRole->slug) {
            case 'top_management':
            case 'middle_management':
                return route('admin.dashboard');

            case 'hr_manager':
            case 'hr_specialist':
                return route('modules.hr.index');

            case 'accounting_manager':
            case 'accountant':
                return route('modules.accounting.index');

            case 'sales_manager':
            case 'sales_representative':
                return route('modules.crm.index');

            case 'support_manager':
            case 'support_agent':
                return route('modules.support.index');

            case 'project_manager':
            case 'team_lead':
                return route('modules.performance.index');

            default:
                // Default to main dashboard for unknown roles
                return route('dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
