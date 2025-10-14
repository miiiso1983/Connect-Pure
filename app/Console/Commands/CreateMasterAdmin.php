<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateMasterAdmin extends Command
{
    protected $signature = 'admin:create-master {email} {name?} {password?}';

    protected $description = 'Create a master admin user with full system access';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name') ?: 'Master Administrator';
        $password = $this->argument('password') ?: 'admin123';

        // Check if user already exists
        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $this->info("Created new user: {$user->name} ({$user->email})");
        } else {
            $this->info("Found existing user: {$user->name} ({$user->email})");
        }

        // Create Master Admin role if it doesn't exist
        $masterRole = Role::where('slug', 'master-admin')->first();

        if (! $masterRole) {
            // Get all possible permissions
            $allPermissions = [
                // Admin permissions
                'admin.view',
                'admin.users.view',
                'admin.users.create',
                'admin.users.edit',
                'admin.users.delete',
                'admin.roles.view',
                'admin.roles.create',
                'admin.roles.edit',
                'admin.roles.delete',
                'admin.settings.view',
                'admin.settings.edit',
                'admin.system.manage',

                // HR permissions
                'hr.view',
                'hr.employees.view',
                'hr.employees.create',
                'hr.employees.edit',
                'hr.employees.delete',
                'hr.leave.view',
                'hr.leave.create',
                'hr.leave.approve',
                'hr.leave.manage',
                'hr.departments.view',
                'hr.departments.manage',
                'hr.attendance.view',
                'hr.attendance.manage',
                'hr.payroll.view',
                'hr.payroll.manage',
                'hr.performance.view',
                'hr.performance.manage',

                // CRM permissions
                'crm.view',
                'crm.leads.view',
                'crm.leads.create',
                'crm.leads.edit',
                'crm.leads.delete',
                'crm.customers.view',
                'crm.customers.create',
                'crm.customers.edit',
                'crm.customers.delete',
                'crm.contacts.view',
                'crm.contacts.create',
                'crm.contacts.edit',
                'crm.contacts.delete',
                'crm.deals.view',
                'crm.deals.create',
                'crm.deals.edit',
                'crm.deals.delete',

                // Performance permissions
                'performance.view',
                'performance.tasks.view',
                'performance.tasks.create',
                'performance.tasks.edit',
                'performance.tasks.delete',
                'performance.reports.view',
                'performance.analytics.view',
                'performance.export',

                // Support permissions
                'support.view',
                'support.tickets.view',
                'support.tickets.create',
                'support.tickets.edit',
                'support.tickets.delete',
                'support.tickets.assign',
                'support.tickets.close',

                // Accounting permissions
                'accounting.view',
                'accounting.invoices.view',
                'accounting.invoices.create',
                'accounting.invoices.edit',
                'accounting.invoices.delete',
                'accounting.expenses.view',
                'accounting.expenses.create',
                'accounting.expenses.edit',
                'accounting.expenses.delete',
                'accounting.reports.view',
                'accounting.reports.export',

                // Project Management permissions
                'projects.view',
                'projects.create',
                'projects.edit',
                'projects.delete',
                'projects.manage',

                // Inventory permissions
                'inventory.view',
                'inventory.products.view',
                'inventory.products.create',
                'inventory.products.edit',
                'inventory.products.delete',
                'inventory.stock.view',
                'inventory.stock.manage',

                // Reports permissions
                'reports.view',
                'reports.create',
                'reports.export',
                'reports.analytics',

                // System permissions
                'system.backup',
                'system.maintenance',
                'system.logs.view',
                'system.settings.manage',
            ];

            $masterRole = Role::create([
                'name' => 'Master Administrator',
                'slug' => 'master-admin',
                'description' => 'Master administrator with full system access to all modules and features',
                'permissions' => $allPermissions,
                'is_active' => true,
                'sort_order' => 0,
                'level' => 0,
                'inherit_permissions' => false,
            ]);

            $this->info('Created Master Admin role with '.count($allPermissions).' permissions');
        } else {
            $this->info('Master Admin role already exists');
        }

        // Assign master role to user
        if (! $user->roles->contains($masterRole->id)) {
            $user->roles()->attach($masterRole->id, [
                'assigned_at' => now(),
                'assigned_by' => $user->id,
            ]);
            $this->info("Assigned Master Admin role to {$user->name}");
        } else {
            $this->info('User already has Master Admin role');
        }

        $this->info("\n=== MASTER ADMIN SETUP COMPLETE ===");
        $this->info("Email: {$user->email}");
        $this->info("Password: {$password}");
        $this->info('Role: Master Administrator');
        $this->info('Permissions: '.count($masterRole->permissions ?? []).' total permissions');
        $this->info('Access Level: FULL SYSTEM ACCESS');
        $this->info("=====================================\n");

        return 0;
    }
}
