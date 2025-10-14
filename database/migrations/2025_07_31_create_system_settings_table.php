<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->string('group')->default('general'); // general, email, security, etc.
            $table->boolean('is_public')->default(false); // can be accessed by non-admin users
            $table->timestamps();

            $table->index(['group', 'key']);
        });

        // Insert default settings
        $defaultSettings = [
            // General Settings
            [
                'key' => 'app_description',
                'value' => 'Connect Pure ERP - Complete Business Management Solution',
                'type' => 'string',
                'description' => 'Application description',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'company_name',
                'value' => 'Connect Pure',
                'type' => 'string',
                'description' => 'Company name',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'company_email',
                'value' => 'info@connectpure.com',
                'type' => 'string',
                'description' => 'Company email address',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'company_phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'string',
                'description' => 'Company phone number',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'company_address',
                'value' => '123 Business Street, Suite 100, City, State 12345',
                'type' => 'string',
                'description' => 'Company address',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'description' => 'Default date format',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i:s',
                'type' => 'string',
                'description' => 'Default time format',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'currency',
                'value' => 'USD',
                'type' => 'string',
                'description' => 'Default currency',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'language',
                'value' => 'en',
                'type' => 'string',
                'description' => 'Default language',
                'group' => 'general',
                'is_public' => true,
            ],

            // Security Settings
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'description' => 'Session lifetime in minutes',
                'group' => 'security',
                'is_public' => false,
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'description' => 'Minimum password length',
                'group' => 'security',
                'is_public' => false,
            ],
            [
                'key' => 'login_attempts',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Maximum login attempts before lockout',
                'group' => 'security',
                'is_public' => false,
            ],
            [
                'key' => 'lockout_duration',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Lockout duration in minutes',
                'group' => 'security',
                'is_public' => false,
            ],
            [
                'key' => 'two_factor_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable two-factor authentication',
                'group' => 'security',
                'is_public' => false,
            ],
            [
                'key' => 'force_https',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Force HTTPS connections',
                'group' => 'security',
                'is_public' => false,
            ],

            // Email Settings
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable email notifications',
                'group' => 'email',
                'is_public' => false,
            ],
            [
                'key' => 'email_queue',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Queue email sending',
                'group' => 'email',
                'is_public' => false,
            ],

            // Maintenance Settings
            [
                'key' => 'maintenance_message',
                'value' => 'System is under maintenance. Please try again later.',
                'type' => 'string',
                'description' => 'Maintenance mode message',
                'group' => 'maintenance',
                'is_public' => true,
            ],
            [
                'key' => 'allowed_ips',
                'value' => '',
                'type' => 'string',
                'description' => 'Allowed IPs during maintenance (comma separated)',
                'group' => 'maintenance',
                'is_public' => false,
            ],

            // System Settings
            [
                'key' => 'backup_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable automatic backups',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'description' => 'Backup frequency (daily, weekly, monthly)',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'log_level',
                'value' => 'info',
                'type' => 'string',
                'description' => 'Application log level',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'debug_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable debug mode',
                'group' => 'system',
                'is_public' => false,
            ],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
