<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class SetupDemoUsers extends Command
{
    protected $signature = 'demo:setup-users';
    protected $description = 'Setup demo users with appropriate roles';

    public function handle()
    {
        $this->info('Setting up demo users with roles...');

        // Create demo roles if they don't exist
        $roles = [
            'hr_manager' => [
                'name' => 'HR Manager',
                'description' => 'Human Resources Manager with full HR access',
                'permissions' => [
                    'hr.view', 'hr.employees.view', 'hr.employees.create', 'hr.employees.edit', 'hr.employees.delete',
                    'hr.leave.view', 'hr.leave.create', 'hr.leave.approve', 'hr.leave.manage',
                    'hr.departments.view', 'hr.departments.manage', 'hr.attendance.view', 'hr.attendance.manage',
                    'hr.payroll.view', 'hr.payroll.manage', 'hr.performance.view', 'hr.performance.manage'
                ]
            ],
            'accounting_manager' => [
                'name' => 'Accounting Manager',
                'description' => 'Accounting Manager with full financial access',
                'permissions' => [
                    'accounting.view', 'accounting.invoices.view', 'accounting.invoices.create', 'accounting.invoices.edit', 'accounting.invoices.delete',
                    'accounting.expenses.view', 'accounting.expenses.create', 'accounting.expenses.edit', 'accounting.expenses.delete',
                    'accounting.reports.view', 'accounting.reports.export'
                ]
            ],
            'sales_manager' => [
                'name' => 'Sales Manager',
                'description' => 'Sales Manager with full CRM access',
                'permissions' => [
                    'crm.view', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.delete',
                    'crm.customers.view', 'crm.customers.create', 'crm.customers.edit', 'crm.customers.delete',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.contacts.delete',
                    'crm.deals.view', 'crm.deals.create', 'crm.deals.edit', 'crm.deals.delete'
                ]
            ],
            'support_manager' => [
                'name' => 'Support Manager',
                'description' => 'Support Manager with full support access',
                'permissions' => [
                    'support.view', 'support.tickets.view', 'support.tickets.create', 'support.tickets.edit', 'support.tickets.delete',
                    'support.tickets.assign', 'support.tickets.close'
                ]
            ]
        ];

        foreach ($roles as $slug => $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'permissions' => $roleData['permissions'],
                    'is_active' => true,
                    'sort_order' => 10,
                    'level' => 1,
                    'inherit_permissions' => false,
                ]
            );
            $this->info("Created/Updated role: {$role->name}");
        }

        // Assign roles to demo users
        $userRoleAssignments = [
            'hr@connectpure.com' => 'hr_manager',
            'hr.manager@connectpure.com' => 'hr_manager',
            'accounting@connectpure.com' => 'accounting_manager',
            'accounting.manager@connectpure.com' => 'accounting_manager',
            'admin@connectpure.com' => 'master-admin',
        ];

        foreach ($userRoleAssignments as $email => $roleSlug) {
            $user = User::where('email', $email)->first();
            $role = Role::where('slug', $roleSlug)->first();

            if ($user && $role) {
                // Remove existing roles
                $user->roles()->detach();
                
                // Assign new role
                $user->roles()->attach($role->id, [
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                ]);
                
                $this->info("Assigned {$role->name} to {$user->name} ({$user->email})");
            } else {
                if (!$user) {
                    $this->warn("User not found: {$email}");
                }
                if (!$role) {
                    $this->warn("Role not found: {$roleSlug}");
                }
            }
        }

        $this->info("\n=== DEMO USERS SETUP COMPLETE ===");
        $this->info("Demo users have been assigned appropriate roles.");
        $this->info("You can now test role-based login redirection.");
        $this->info("=====================================");

        return 0;
    }
}
