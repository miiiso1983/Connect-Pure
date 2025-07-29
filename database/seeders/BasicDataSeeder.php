<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BasicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding basic data...');

        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@connectpure.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create HR manager
        User::updateOrCreate(
            ['email' => 'hr@connectpure.com'],
            [
                'name' => 'HR Manager',
                'email' => 'hr@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create accounting manager
        User::updateOrCreate(
            ['email' => 'accounting@connectpure.com'],
            [
                'name' => 'Accounting Manager',
                'email' => 'accounting@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Basic data seeding completed!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@connectpure.com / password');
        $this->command->info('HR: hr@connectpure.com / password');
        $this->command->info('Accounting: accounting@connectpure.com / password');
    }
}
