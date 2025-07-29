<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Master Admin Role with ALL permissions
        $masterAdminRole = Role::updateOrCreate(
            ['slug' => 'master-admin'],
            [
                'name' => 'Master Administrator',
                'description' => 'Super administrator with unrestricted access to all system functions and modules.',
                'sort_order' => 0, // Highest priority
                'is_active' => true,
                'level' => 0, // Top level
                'inherit_permissions' => false,
                'permissions' => $this->getAllSystemPermissions(),
            ]
        );

        // Create Super Admin Role (backup admin)
        $superAdminRole = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrator',
                'description' => 'System administrator with full access to all modules and administrative functions.',
                'sort_order' => 1,
                'is_active' => true,
                'level' => 1,
                'inherit_permissions' => false,
                'permissions' => $this->getAllSystemPermissions(),
            ]
        );

        // Create Master Admin User
        $masterAdmin = User::updateOrCreate(
            ['email' => 'master@connectpure.com'],
            [
                'name' => 'Master Administrator',
                'password' => Hash::make('MasterAdmin@2024'),
                'email_verified_at' => now(),
            ]
        );

        // Create Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@connectpure.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdmin@2024'),
                'email_verified_at' => now(),
            ]
        );

        // Create System Admin User (your main account)
        $systemAdmin = User::updateOrCreate(
            ['email' => 'admin@connectpure.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@2024'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Master Admin role to Master Admin user
        if (!$masterAdmin->hasRole('master-admin')) {
            $masterAdmin->assignRole('master-admin');
        }

        // Assign Super Admin role to Super Admin user
        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        // Assign both roles to System Admin for maximum access
        if (!$systemAdmin->hasRole('master-admin')) {
            $systemAdmin->assignRole('master-admin');
        }
        if (!$systemAdmin->hasRole('top_management')) {
            $systemAdmin->assignRole('top_management');
        }

        $this->command->info('✅ Master Administrator created: master@connectpure.com / MasterAdmin@2024');
        $this->command->info('✅ Super Administrator created: superadmin@connectpure.com / SuperAdmin@2024');
        $this->command->info('✅ System Administrator updated: admin@connectpure.com / Admin@2024');
        $this->command->info('🔑 All accounts have FULL SYSTEM ACCESS');
    }

    /**
     * Get all system permissions for complete access.
     */
    private function getAllSystemPermissions(): array
    {
        return [
            // System Administration
            'system.admin',
            'system.settings',
            'system.maintenance',
            'system.backup',
            'system.logs',
            'system.security',

            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage',
            'users.roles',
            'users.permissions',

            // Role Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.manage',
            'roles.permissions',

            // HR Module - Complete Access
            'hr.view',
            'hr.admin',
            'hr.employees.view',
            'hr.employees.create',
            'hr.employees.edit',
            'hr.employees.delete',
            'hr.employees.manage',
            'hr.departments.view',
            'hr.departments.create',
            'hr.departments.edit',
            'hr.departments.delete',
            'hr.departments.manage',
            'hr.roles.view',
            'hr.roles.create',
            'hr.roles.edit',
            'hr.roles.delete',
            'hr.roles.manage',
            'hr.leave.view',
            'hr.leave.create',
            'hr.leave.edit',
            'hr.leave.delete',
            'hr.leave.approve',
            'hr.leave.manage',
            'hr.attendance.view',
            'hr.attendance.create',
            'hr.attendance.edit',
            'hr.attendance.delete',
            'hr.attendance.manage',
            'hr.payroll.view',
            'hr.payroll.create',
            'hr.payroll.edit',
            'hr.payroll.delete',
            'hr.payroll.process',
            'hr.payroll.approve',
            'hr.payroll.manage',
            'hr.performance.view',
            'hr.performance.create',
            'hr.performance.edit',
            'hr.performance.delete',
            'hr.performance.manage',

            // Accounting Module - Complete Access
            'accounting.view',
            'accounting.admin',
            'accounting.accounts.view',
            'accounting.accounts.create',
            'accounting.accounts.edit',
            'accounting.accounts.delete',
            'accounting.accounts.manage',
            'accounting.customers.view',
            'accounting.customers.create',
            'accounting.customers.edit',
            'accounting.customers.delete',
            'accounting.customers.manage',
            'accounting.vendors.view',
            'accounting.vendors.create',
            'accounting.vendors.edit',
            'accounting.vendors.delete',
            'accounting.vendors.manage',
            'accounting.invoices.view',
            'accounting.invoices.create',
            'accounting.invoices.edit',
            'accounting.invoices.delete',
            'accounting.invoices.send',
            'accounting.invoices.approve',
            'accounting.invoices.manage',
            'accounting.expenses.view',
            'accounting.expenses.create',
            'accounting.expenses.edit',
            'accounting.expenses.delete',
            'accounting.expenses.approve',
            'accounting.expenses.manage',
            'accounting.payments.view',
            'accounting.payments.create',
            'accounting.payments.edit',
            'accounting.payments.delete',
            'accounting.payments.manage',
            'accounting.reports.view',
            'accounting.reports.generate',
            'accounting.reports.export',
            'accounting.reports.manage',

            // CRM Module - Complete Access
            'crm.view',
            'crm.admin',
            'crm.contacts.view',
            'crm.contacts.create',
            'crm.contacts.edit',
            'crm.contacts.delete',
            'crm.contacts.manage',
            'crm.communications.view',
            'crm.communications.create',
            'crm.communications.edit',
            'crm.communications.delete',
            'crm.communications.manage',
            'crm.followups.view',
            'crm.followups.create',
            'crm.followups.edit',
            'crm.followups.delete',
            'crm.followups.manage',

            // Support Module - Complete Access
            'support.view',
            'support.admin',
            'support.tickets.view',
            'support.tickets.create',
            'support.tickets.edit',
            'support.tickets.delete',
            'support.tickets.assign',
            'support.tickets.close',
            'support.tickets.manage',

            // Performance Module - Complete Access
            'performance.view',
            'performance.admin',
            'performance.tasks.view',
            'performance.tasks.create',
            'performance.tasks.edit',
            'performance.tasks.delete',
            'performance.tasks.assign',
            'performance.tasks.manage',
            'performance.metrics.view',
            'performance.metrics.create',
            'performance.metrics.edit',
            'performance.metrics.delete',
            'performance.metrics.manage',

            // Dashboard and Reports
            'dashboard.view',
            'dashboard.admin',
            'reports.view',
            'reports.create',
            'reports.export',
            'reports.manage',

            // Settings and Configuration
            'settings.view',
            'settings.edit',
            'settings.manage',
            'config.view',
            'config.edit',
            'config.manage',

            // File Management
            'files.view',
            'files.upload',
            'files.download',
            'files.delete',
            'files.manage',

            // API Access
            'api.access',
            'api.admin',

            // Audit and Logs
            'audit.view',
            'audit.manage',
            'logs.view',
            'logs.manage',

            // Backup and Maintenance
            'backup.create',
            'backup.restore',
            'backup.manage',
            'maintenance.mode',
            'maintenance.manage',
        ];
    }
}
