<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestAuthentication extends Command
{
    protected $signature = 'auth:test {email}';
    protected $description = 'Test authentication and role-based redirection for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->with('roles')->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $this->info("=== AUTHENTICATION TEST ===");
        $this->info("User: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("ID: {$user->id}");
        
        // Test role detection
        $primaryRole = $user->roles->first();
        if ($primaryRole) {
            $this->info("Primary Role: {$primaryRole->name} ({$primaryRole->slug})");
            
            // Test redirection logic
            $redirectUrl = $this->getRedirectUrlBasedOnRole($user);
            $this->info("Redirect URL: {$redirectUrl}");
            
            // Test permissions
            $this->info("Has master-admin role: " . ($user->hasRole('master-admin') ? 'Yes' : 'No'));
            
        } else {
            $this->warn("No roles assigned to this user");
        }
        
        $this->info("=============================");
        
        return 0;
    }

    /**
     * Get redirect URL based on user's role (copied from AuthenticatedSessionController).
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
}
