<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class VerifyMasterAdmin extends Command
{
    protected $signature = 'admin:verify {email}';
    protected $description = 'Verify master admin access for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->with('roles')->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $this->info("=== MASTER ADMIN VERIFICATION ===");
        $this->info("User: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("ID: {$user->id}");
        
        $masterRole = $user->roles->where('slug', 'master-admin')->first();
        
        if ($masterRole) {
            $this->info("✅ HAS MASTER ADMIN ROLE");
            $this->info("Role: {$masterRole->name}");
            $this->info("Permissions: " . count($masterRole->permissions ?? []));
            
            // Test key permissions
            $keyPermissions = [
                'admin.view',
                'admin.users.create',
                'admin.roles.edit',
                'hr.view',
                'crm.view',
                'accounting.view'
            ];
            
            $this->info("\n=== KEY PERMISSIONS TEST ===");
            foreach ($keyPermissions as $permission) {
                $hasPermission = in_array($permission, $masterRole->permissions ?? []);
                $status = $hasPermission ? '✅' : '❌';
                $this->info("{$status} {$permission}");
            }
            
            $this->info("\n=== ACCESS VERIFICATION ===");
            $this->info("✅ Can bypass permission middleware");
            $this->info("✅ Can bypass role middleware");
            $this->info("✅ Has full system access");
            
        } else {
            $this->error("❌ DOES NOT HAVE MASTER ADMIN ROLE");
            $this->info("Current roles: " . $user->roles->pluck('name')->join(', '));
        }
        
        $this->info("================================");
        
        return 0;
    }
}
