<?php

namespace App\Http\Middleware;

use App\Models\SystemUserRole;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Owner
{
    use ResponseTrait;

    /**
     * Role slugs that are permitted to access the owner portal.
     * These are secondary roles assigned via system_user_roles.
     */
    private const OWNER_PORTAL_ROLES = [
        'owner', 'director', 'landlord', 'tenant_manager',
        'auditor', 'compliance_officer', 'secretary', 'finance_manager',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->wantsJson()) {
                return $this->error([], __('Unauthorized'));
            }
            return redirect()->route('login');
        }

        // Primary role check (unchanged)
        if ($user->role == USER_ROLE_OWNER || $user->role == USER_ROLE_TEAM_MEMBER) {
            return $next($request);
        }

        // Multi-role: allow assigned owner-portal roles only when the assignment is active.
        $activeRole = session('active_role');
        if ($activeRole && in_array($activeRole, self::OWNER_PORTAL_ROLES, true)) {
            $assignment = SystemUserRole::where('user_id', $user->id)
                ->where('role_slug', $activeRole)
                ->where('is_active', true)
                ->first();

            if ($assignment) {
                session(['active_role_owner_user_id' => $assignment->owner_user_id]);
                return $next($request);
            }
        }

        if ($request->wantsJson()) {
            return $this->error([], __('Unauthorized'));
        }

        abort(403);
    }
}
