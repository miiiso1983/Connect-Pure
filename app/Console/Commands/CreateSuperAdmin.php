<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:create-super 
                            {--name= : The name of the admin user}
                            {--email= : The email of the admin user}
                            {--password= : The password for the admin user}
                            {--force : Force creation without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Create a super administrator account with full system access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Creating Super Administrator Account');
        $this->info('=====================================');

        // Get user input
        $name = $this->option('name') ?: $this->ask('Enter admin name', 'Super Administrator');
        $email = $this->option('email') ?: $this->ask('Enter admin email', 'admin@connectpure.com');
        $password = $this->option('password') ?: $this->secret('Enter admin password (min 8 characters)');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("   • $error");
            }

            return 1;
        }

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            if (! $this->option('force') && ! $this->confirm("User with email '$email' already exists. Update existing user?")) {
                $this->info('Operation cancelled.');

                return 0;
            }
        }

        // Ensure master-admin role exists
        $this->ensureMasterAdminRole();

        // Create or update user
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        // Assign master-admin role
        if (! $user->hasRole('master-admin')) {
            $user->assignRole('master-admin');
            $this->info('✅ Assigned master-admin role');
        }

        // Also assign top_management role for additional access
        if (! $user->hasRole('top_management')) {
            $user->assignRole('top_management');
            $this->info('✅ Assigned top_management role');
        }

        $this->info('');
        $this->info('🎉 Super Administrator Account Created Successfully!');
        $this->info('================================================');
        $this->info("👤 Name: $name");
        $this->info("📧 Email: $email");
        $this->info('🔑 Password: [HIDDEN]');
        $this->info('🛡️  Roles: Master Admin, Top Management');
        $this->info('🔓 Access: FULL SYSTEM ACCESS');
        $this->info('');
        $this->info('🌐 Login URL: '.config('app.url').'/login');
        $this->info('');
        $this->warn('⚠️  Keep these credentials secure!');

        return 0;
    }

    /**
     * Ensure master-admin role exists with all permissions.
     */
    private function ensureMasterAdminRole(): void
    {
        $role = Role::updateOrCreate(
            ['slug' => 'master-admin'],
            [
                'name' => 'Master Administrator',
                'description' => 'Super administrator with unrestricted access to all system functions.',
                'sort_order' => 0,
                'is_active' => true,
                'level' => 0,
                'inherit_permissions' => false,
                'permissions' => $this->getAllSystemPermissions(),
            ]
        );

        $this->info('✅ Master Admin role ensured');
    }

    /**
     * Get all system permissions.
     */
    private function getAllSystemPermissions(): array
    {
        return [
            // System Administration
            'system.admin', 'system.settings', 'system.maintenance', 'system.backup', 'system.logs', 'system.security',

            // User Management
            'users.view', 'users.create', 'users.edit', 'users.delete', 'users.manage', 'users.roles', 'users.permissions',

            // Role Management
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'roles.manage', 'roles.permissions',

            // HR Module
            'hr.view', 'hr.admin', 'hr.employees.view', 'hr.employees.create', 'hr.employees.edit', 'hr.employees.delete', 'hr.employees.manage',
            'hr.departments.view', 'hr.departments.create', 'hr.departments.edit', 'hr.departments.delete', 'hr.departments.manage',
            'hr.leave.view', 'hr.leave.create', 'hr.leave.edit', 'hr.leave.delete', 'hr.leave.approve', 'hr.leave.manage',
            'hr.attendance.view', 'hr.attendance.create', 'hr.attendance.edit', 'hr.attendance.delete', 'hr.attendance.manage',
            'hr.payroll.view', 'hr.payroll.create', 'hr.payroll.edit', 'hr.payroll.delete', 'hr.payroll.process', 'hr.payroll.approve', 'hr.payroll.manage',
            'hr.performance.view', 'hr.performance.create', 'hr.performance.edit', 'hr.performance.delete', 'hr.performance.manage',

            // Accounting Module
            'accounting.view', 'accounting.admin', 'accounting.accounts.view', 'accounting.accounts.create', 'accounting.accounts.edit', 'accounting.accounts.delete', 'accounting.accounts.manage',
            'accounting.customers.view', 'accounting.customers.create', 'accounting.customers.edit', 'accounting.customers.delete', 'accounting.customers.manage',
            'accounting.vendors.view', 'accounting.vendors.create', 'accounting.vendors.edit', 'accounting.vendors.delete', 'accounting.vendors.manage',
            'accounting.invoices.view', 'accounting.invoices.create', 'accounting.invoices.edit', 'accounting.invoices.delete', 'accounting.invoices.send', 'accounting.invoices.approve', 'accounting.invoices.manage',
            'accounting.expenses.view', 'accounting.expenses.create', 'accounting.expenses.edit', 'accounting.expenses.delete', 'accounting.expenses.approve', 'accounting.expenses.manage',
            'accounting.payments.view', 'accounting.payments.create', 'accounting.payments.edit', 'accounting.payments.delete', 'accounting.payments.manage',
            'accounting.reports.view', 'accounting.reports.generate', 'accounting.reports.export', 'accounting.reports.manage',

            // CRM Module
            'crm.view', 'crm.admin', 'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.contacts.delete', 'crm.contacts.manage',
            'crm.communications.view', 'crm.communications.create', 'crm.communications.edit', 'crm.communications.delete', 'crm.communications.manage',
            'crm.followups.view', 'crm.followups.create', 'crm.followups.edit', 'crm.followups.delete', 'crm.followups.manage',

            // Support Module
            'support.view', 'support.admin', 'support.tickets.view', 'support.tickets.create', 'support.tickets.edit', 'support.tickets.delete', 'support.tickets.assign', 'support.tickets.close', 'support.tickets.manage',

            // Performance Module
            'performance.view', 'performance.admin', 'performance.tasks.view', 'performance.tasks.create', 'performance.tasks.edit', 'performance.tasks.delete', 'performance.tasks.assign', 'performance.tasks.manage',
            'performance.metrics.view', 'performance.metrics.create', 'performance.metrics.edit', 'performance.metrics.delete', 'performance.metrics.manage',

            // Dashboard and Reports
            'dashboard.view', 'dashboard.admin', 'reports.view', 'reports.create', 'reports.export', 'reports.manage',

            // Settings and Configuration
            'settings.view', 'settings.edit', 'settings.manage', 'config.view', 'config.edit', 'config.manage',

            // File Management
            'files.view', 'files.upload', 'files.download', 'files.delete', 'files.manage',

            // API Access
            'api.access', 'api.admin',

            // Audit and Logs
            'audit.view', 'audit.manage', 'logs.view', 'logs.manage',

            // Backup and Maintenance
            'backup.create', 'backup.restore', 'backup.manage', 'maintenance.mode', 'maintenance.manage',
        ];
    }
}
