<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permissions  Comma-separated list of permissions
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $requiredPermissions = explode('|', $permissions);

        // Master admin bypass - users with master-admin role have access to everything
        if ($user->hasRole('master-admin')) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        if (!$user->hasAnyPermission($requiredPermissions)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied',
                    'message' => __('roles.insufficient_permissions'),
                    'required_permissions' => $requiredPermissions,
                    'user_permissions' => $user->getAllPermissions(),
                ], 403);
            }

            abort(403, __('roles.insufficient_permissions'));
        }

        return $next($request);
    }
}
