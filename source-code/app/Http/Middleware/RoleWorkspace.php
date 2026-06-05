<?php

namespace App\Http\Middleware;

use App\Models\SystemUserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleWorkspace
{
    private const WORKSPACE_ROLES = [
        'director',
        'finance_manager',
        'landlord',
        'tenant_manager',
        'auditor',
        'compliance_officer',
        'secretary',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $role = $request->route('role') ?: ($request->route()->defaults['role'] ?? null);

        if (!$user || !in_array($role, self::WORKSPACE_ROLES, true)) {
            abort(403);
        }

        $assignment = SystemUserRole::where('user_id', $user->id)
            ->where('role_slug', $role)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {
            abort(403);
        }

        session([
            'active_role' => $role,
            'active_role_owner_user_id' => $assignment->owner_user_id,
        ]);

        return $next($request);
    }
}
