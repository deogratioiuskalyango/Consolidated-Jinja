<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccountantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow primary accountant role OR multi-role switch to 'accountant'
        $isAccountant = ($user->role == USER_ROLE_ACCOUNTANT)
            || (session('active_role') === 'accountant');

        if (!$isAccountant) {
            abort(403, 'Access denied.');
        }

        $accountant = $user->accountant;

        if (!$accountant || $accountant->status != ACCOUNTANT_STATUS_ACTIVE) {
            // Multi-role user: redirect gracefully without logout
            if ($user->role != USER_ROLE_ACCOUNTANT) {
                session(['active_role' => null]);
                return redirect()->route('role.select.index')
                    ->with('error', 'You do not have an active accountant account.');
            }
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your accountant account has been suspended. Contact the administrator.');
        }

        if (
            $accountant->force_password_change
            && !$request->routeIs('accountant.change-password')
            && !$request->routeIs('accountant.change-password.update')
        ) {
            return redirect()->route('accountant.change-password')
                ->with('warning', 'Please change your password before continuing.');
        }

        return $next($request);
    }
}
