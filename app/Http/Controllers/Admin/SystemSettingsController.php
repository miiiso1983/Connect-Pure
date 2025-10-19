<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemSettingsController extends Controller
{
    protected Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->middleware(['auth', 'role:master-admin|top_management']);
        $this->settings = $settings;
    }

    /**
     * Display system settings dashboard
     */
    public function index()
    {
        $systemInfo = $this->getSystemInfo();
        $settings = $this->getSystemSettings();

        return view('admin.system-settings.index', compact('systemInfo', 'settings'));
    }

    /**
     * Show general settings
     */
    public function general()
    {
        $settings = $this->getGeneralSettings();

        return view('admin.system-settings.general', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'currency' => 'required|string|max:3',
            'language' => 'required|string|max:5',
        ]);

        $this->updateEnvFile([
            'APP_NAME' => $request->app_name,
            'APP_TIMEZONE' => $request->timezone,
        ]);

        $this->updateSettings([
            'app_description' => $request->app_description,
            'company_name' => $request->company_name,
            'company_email' => $request->company_email,
            'company_phone' => $request->company_phone,
            'company_address' => $request->company_address,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
            'currency' => $request->currency,
            'language' => $request->language,
        ]);

        return redirect()->back()->with('success', 'General settings updated successfully!');
    }

    /**
     * Show email settings
     */
    public function email()
    {
        $settings = $this->getEmailSettings();

        return view('admin.system-settings.email', compact('settings'));
    }

    /**
     * Update email settings
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $this->updateEnvFile([
            'MAIL_MAILER' => $request->mail_driver,
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_USERNAME' => $request->mail_username,
            'MAIL_PASSWORD' => $request->mail_password,
            'MAIL_ENCRYPTION' => $request->mail_encryption,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => $request->mail_from_name,
        ]);

        return redirect()->back()->with('success', 'Email settings updated successfully!');
    }

    /**
     * Show database settings
     */
    public function database()
    {
        $dbInfo = $this->getDatabaseInfo();

        return view('admin.system-settings.database', compact('dbInfo'));
    }

    /**
     * Show security settings
     */
    public function security()
    {
        $settings = $this->getSecuritySettings();

        return view('admin.system-settings.security', compact('settings'));
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'session_lifetime' => 'required|integer|min:1|max:1440',
            'password_min_length' => 'required|integer|min:6|max:50',
            'login_attempts' => 'required|integer|min:1|max:10',
            'lockout_duration' => 'required|integer|min:1|max:60',
            'two_factor_enabled' => 'boolean',
            'force_https' => 'boolean',
        ]);

        $this->updateSettings([
            'session_lifetime' => $request->session_lifetime,
            'password_min_length' => $request->password_min_length,
            'login_attempts' => $request->login_attempts,
            'lockout_duration' => $request->lockout_duration,
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'force_https' => $request->boolean('force_https'),
        ]);

        return redirect()->back()->with('success', 'Security settings updated successfully!');
    }

    /**
     * Show maintenance settings
     */
    public function maintenance()
    {
        $isDown = app()->isDownForMaintenance();
        $settings = $this->getMaintenanceSettings();

        return view('admin.system-settings.maintenance', compact('isDown', 'settings'));
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance(Request $request)
    {
        $isDown = app()->isDownForMaintenance();

        if ($isDown) {
            Artisan::call('up');
            $message = 'Application is now live!';
        } else {
            $secret = $request->input('secret', 'admin-access');
            Artisan::call('down', [
                '--secret' => $secret,
                '--render' => 'errors::503',
            ]);
            $message = 'Application is now in maintenance mode!';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Clear system caches
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->back()->with('success', 'All caches cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error clearing caches: '.$e->getMessage());
        }
    }

    /**
     * Optimize system
     */
    public function optimize()
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return redirect()->back()->with('success', 'System optimized successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error optimizing system: '.$e->getMessage());
        }
    }

    /**
     * Get system information
     */
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_space' => $this->getDiskSpace(),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
        ];
    }

    /**
     * Get disk space information
     */
    private function getDiskSpace()
    {
        $bytes = disk_free_space('/');
        $total = disk_total_space('/');

        return [
            'free' => $this->formatBytes($bytes),
            'total' => $this->formatBytes($total),
            'used_percentage' => round((($total - $bytes) / $total) * 100, 2),
        ];
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }

    /**
     * Get system settings
     */
    private function getSystemSettings()
    {
        return $this->settings->all();
    }

    /**
     * Update settings in database
     */
    private function updateSettings(array $settings)
    {
        $this->settings->setMany($settings);
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        foreach ($data as $key => $value) {
            $value = is_string($value) ? '"'.$value.'"' : $value;

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envFile, $envContent);
    }

    /**
     * Get general settings
     */
    private function getGeneralSettings()
    {
        return [
            'app_name' => config('app.name'),
            'app_description' => $this->getSetting('app_description', 'Connect Pure ERP System'),
            'company_name' => $this->getSetting('company_name', 'Connect Pure'),
            'company_email' => $this->getSetting('company_email', 'info@connectpure.com'),
            'company_phone' => $this->getSetting('company_phone', ''),
            'company_address' => $this->getSetting('company_address', ''),
            'timezone' => config('app.timezone'),
            'date_format' => $this->getSetting('date_format', 'Y-m-d'),
            'time_format' => $this->getSetting('time_format', 'H:i:s'),
            'currency' => $this->getSetting('currency', 'USD'),
            'language' => $this->getSetting('language', 'en'),
        ];
    }

    /**
     * Get email settings
     */
    private function getEmailSettings()
    {
        return [
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_password' => config('mail.mailers.smtp.password'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];
    }

    /**
     * Get database information
     */
    private function getDatabaseInfo()
    {
        return [
            'connection' => config('database.default'),
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'tables_count' => DB::select('SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?', [config('database.connections.mysql.database')])[0]->count ?? 0,
        ];
    }

    /**
     * Get security settings
     */
    private function getSecuritySettings()
    {
        return [
            'session_lifetime' => $this->getSetting('session_lifetime', 120),
            'password_min_length' => $this->getSetting('password_min_length', 8),
            'login_attempts' => $this->getSetting('login_attempts', 5),
            'lockout_duration' => $this->getSetting('lockout_duration', 15),
            'two_factor_enabled' => $this->getSetting('two_factor_enabled', false),
            'force_https' => $this->getSetting('force_https', false),
        ];
    }

    /**
     * Get maintenance settings
     */
    private function getMaintenanceSettings()
    {
        return [
            'maintenance_message' => $this->getSetting('maintenance_message', 'System is under maintenance. Please try again later.'),
            'allowed_ips' => $this->getSetting('allowed_ips', ''),
        ];
    }

    /**
     * Get setting value
     */
    private function getSetting($key, $default = null)
    {
        return $this->settings->get($key, $default);
    }
}
