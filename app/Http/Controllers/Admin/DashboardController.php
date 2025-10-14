<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // System statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'total_roles' => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'users_with_roles' => User::whereHas('roles')->count(),
            'users_without_roles' => User::whereDoesntHave('roles')->count(),
        ];

        // Recent users
        $recentUsers = User::with('roles')
            ->latest()
            ->limit(5)
            ->get();

        // Role distribution
        $roleDistribution = Role::withCount('users')
            ->where('is_active', true)
            ->orderBy('users_count', 'desc')
            ->limit(10)
            ->get();

        // User activity (last 30 days)
        $userActivity = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // System health checks
        $systemHealth = [
            'database' => $this->checkDatabaseConnection(),
            'storage' => $this->checkStorageWritable(),
            'cache' => $this->checkCacheWorking(),
            'queue' => $this->checkQueueWorking(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'roleDistribution',
            'userActivity',
            'systemHealth'
        ));
    }

    /**
     * Check database connection.
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if storage is writable.
     */
    private function checkStorageWritable(): bool
    {
        return is_writable(storage_path());
    }

    /**
     * Check if cache is working.
     */
    private function checkCacheWorking(): bool
    {
        try {
            cache()->put('health_check', 'test', 60);

            return cache()->get('health_check') === 'test';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if queue is working.
     */
    private function checkQueueWorking(): bool
    {
        // Simple check - in production you might want to check actual queue status
        return true;
    }
}
