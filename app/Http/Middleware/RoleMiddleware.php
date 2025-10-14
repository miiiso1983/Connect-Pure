<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles  Comma-separated list of roles
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $allowedRoles = explode('|', $roles);

        // Master admin bypass - users with master-admin role have access to everything
        if ($user->hasRole('master-admin')) {
            return $next($request);
        }

        if (! $user->hasAnyRole($allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied',
                    'message' => __('roles.insufficient_permissions'),
                ], 403);
            }

            abort(403, __('roles.insufficient_permissions'));
        }

        return $next($request);
    }
}
