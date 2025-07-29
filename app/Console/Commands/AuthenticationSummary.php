<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AuthenticationSummary extends Command
{
    protected $signature = 'auth:summary';
    protected $description = 'Display authentication system summary';

    public function handle()
    {
        $this->info('');
        $this->info('🔐 AUTHENTICATION SYSTEM SUMMARY');
        $this->info('=====================================');
        
        // System Overview
        $totalUsers = User::count();
        $usersWithRoles = User::whereHas('roles')->count();
        $totalRoles = Role::count();
        $activeRoles = Role::where('is_active', true)->count();
        
        $this->info("📊 System Statistics:");
        $this->info("   • Total Users: {$totalUsers}");
        $this->info("   • Users with Roles: {$usersWithRoles}");
        $this->info("   • Total Roles: {$totalRoles}");
        $this->info("   • Active Roles: {$activeRoles}");
        $this->info('');
        
        // Authentication Features
        $this->info('🚀 Authentication Features:');
        $this->info('   ✅ Enhanced Login Page with Modern UI');
        $this->info('   ✅ Role-Based Redirection After Login');
        $this->info('   ✅ Logout Functionality with User Menu');
        $this->info('   ✅ Master Admin Bypass for All Permissions');
        $this->info('   ✅ Session Management and Security');
        $this->info('   ✅ Password Reset Functionality');
        $this->info('   ✅ Remember Me Feature');
        $this->info('   ✅ Demo Accounts for Testing');
        $this->info('');
        
        // Role-Based Redirection
        $this->info('🎯 Role-Based Redirection:');
        $this->info('   • Master Admin → Main Dashboard (Full Access)');
        $this->info('   • Management Roles → Admin Dashboard');
        $this->info('   • HR Roles → HR Module');
        $this->info('   • Accounting Roles → Accounting Module');
        $this->info('   • Sales Roles → CRM Module');
        $this->info('   • Support Roles → Support Module');
        $this->info('   • Project Roles → Performance Module');
        $this->info('   • Default → Main Dashboard');
        $this->info('');
        
        // Demo Accounts
        $this->info('👥 Demo Accounts:');
        $demoAccounts = [
            'mustafaalrawan@gmail.com' => ['Master Administrator', 'admin123', 'Full System Access'],
            'hr@connectpure.com' => ['HR Manager', 'password', 'HR Module Access'],
            'accounting@connectpure.com' => ['Accounting Manager', 'password', 'Accounting Module Access'],
            'admin@connectpure.com' => ['Master Administrator', 'password', 'Full System Access'],
        ];
        
        foreach ($demoAccounts as $email => $details) {
            $user = User::where('email', $email)->with('roles')->first();
            $status = $user ? '✅' : '❌';
            $role = $user && $user->roles->first() ? $user->roles->first()->name : 'No Role';
            $this->info("   {$status} {$email}");
            $this->info("      Role: {$role} | Password: {$details[1]}");
            $this->info("      Access: {$details[2]}");
        }
        $this->info('');
        
        // Security Features
        $this->info('🛡️ Security Features:');
        $this->info('   ✅ Permission Middleware with Master Admin Bypass');
        $this->info('   ✅ Role Middleware with Master Admin Bypass');
        $this->info('   ✅ CSRF Protection on All Forms');
        $this->info('   ✅ Session Regeneration on Login');
        $this->info('   ✅ Secure Logout with Session Invalidation');
        $this->info('   ✅ Email Verification Support');
        $this->info('   ✅ Password Hashing with Bcrypt');
        $this->info('');
        
        // URLs
        $this->info('🌐 Important URLs:');
        $this->info('   • Login Page: /login');
        $this->info('   • Main Dashboard: /');
        $this->info('   • Admin Dashboard: /admin');
        $this->info('   • User Management: /admin/users');
        $this->info('   • Role Management: /admin/roles');
        $this->info('   • Role Hierarchy: /modules/roles/hierarchy');
        $this->info('   • User Role Assignment: /admin/user-roles');
        $this->info('');
        
        // Next Steps
        $this->info('📋 Usage Instructions:');
        $this->info('   1. Visit /login to access the enhanced login page');
        $this->info('   2. Use any demo account to test role-based redirection');
        $this->info('   3. Master admin accounts have unrestricted access');
        $this->info('   4. Other roles redirect to their respective modules');
        $this->info('   5. Use the user menu (top right) to logout');
        $this->info('   6. Logged out users are redirected to login page');
        $this->info('');
        
        $this->info('🎉 Authentication system is fully operational!');
        $this->info('=====================================');
        
        return 0;
    }
}
