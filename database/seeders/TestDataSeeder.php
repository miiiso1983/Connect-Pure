<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding test data...');

        // Create test users
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Accounting Manager',
                'email' => 'accounting@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Test User',
                'email' => 'test@connectpure.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Run currency and tax seeder
        $this->call(CurrencyAndTaxSeeder::class);

        $this->command->info('Test data seeding completed!');
        $this->command->info('');
        $this->command->info('Test Login Credentials:');
        $this->command->info('Admin: admin@connectpure.com / password');
        $this->command->info('HR: hr@connectpure.com / password');
        $this->command->info('Accounting: accounting@connectpure.com / password');
        $this->command->info('Test User: test@connectpure.com / password');
    }
}
