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
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        // If no authenticated user, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        // If no role specified in middleware parameters, allow access
        if (!$role) {
            return $next($request);
        }

        // Check if user has the required role
        if ($user->role === $role) {
            return $next($request);
        }

        // Handle multiple roles separated by pipe (e.g., 'admin|warehouse_staff')
        $roles = explode('|', $role);
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // User doesn't have the required role
        abort(403, "Unauthorized access. You don't have permission to access this resource. Required role: {$role}");
    }
}
