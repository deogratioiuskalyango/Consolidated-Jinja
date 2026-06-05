<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShareholderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow primary shareholder role OR multi-role switch to 'shareholder'
        $isShareholder = ($user->role == USER_ROLE_SHAREHOLDER)
            || (session('active_role') === 'shareholder');

        if (!$isShareholder) {
            abort(403, 'Access denied.');
        }

        $shareholder = $user->shareholder;

        if (!$shareholder || $shareholder->status != SHAREHOLDER_STATUS_ACTIVE) {
            // Multi-role user switching in: don't log them out, redirect back
            if ($user->role != USER_ROLE_SHAREHOLDER) {
                session(['active_role' => null]);
                return redirect()->route('role.select.index')
                    ->with('error', 'You do not have an active shareholder account.');
            }
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been suspended. Contact the administrator.');
        }

        // Force password change on first login
        if (
            $shareholder->force_password_change
            && !$request->routeIs('shareholder.change-password')
            && !$request->routeIs('shareholder.change-password.update')
        ) {
            return redirect()->route('shareholder.change-password')
                ->with('warning', 'Please change your password before continuing.');
        }

        return $next($request);
    }
}
