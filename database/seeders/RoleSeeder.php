<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Top Management',
                'slug' => 'top_management',
                'description' => 'Executive leadership with full system access and strategic oversight responsibilities.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Middle Management',
                'slug' => 'middle_management',
                'description' => 'Department managers with supervisory responsibilities and team management access.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Sales Team',
                'slug' => 'sales_team',
                'description' => 'Sales professionals focused on customer relationship management and sales activities.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Technical Team',
                'slug' => 'technical_team',
                'description' => 'Technical staff responsible for project execution and performance tracking.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Accounting',
                'slug' => 'accounting',
                'description' => 'Financial professionals managing accounting operations and financial reporting.',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'slug' => 'hr',
                'description' => 'Human resources staff managing employee lifecycle and organizational development.',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        $defaultPermissions = Role::getDefaultPermissions();

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'sort_order' => $roleData['sort_order'],
                    'is_active' => $roleData['is_active'],
                    'permissions' => $defaultPermissions[$roleData['slug']] ?? [],
                ]
            );

            $this->command->info("Created/Updated role: {$role->name}");
        }

        $this->command->info('Role seeding completed successfully!');
    }
}
