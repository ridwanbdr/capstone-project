<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();
        
        // If no role is set, redirect to login with error
        if (empty($user->role)) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account does not have a valid role. Please contact administrator.');
        }

        // Admin has access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has one of the required roles
        if (!in_array(strtolower($user->role), array_map('strtolower', $roles))) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}

