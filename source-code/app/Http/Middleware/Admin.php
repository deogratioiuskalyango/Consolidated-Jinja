<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->wantsJson()) {
                return response()->json(['message' => __('Unauthorized')], 401);
            }
            return redirect()->route('login');
        }

        // Admin access is granted ONLY via the primary role (users.role = 4).
        // The system_user_roles table is intentionally NOT checked here to prevent
        // privilege escalation where an owner/shareholder assigns themselves admin access.
        if ($user->role == USER_ROLE_ADMIN) {
            session(['active_role' => 'admin']);
            return $next($request);
        }

        abort(403);
    }
}
