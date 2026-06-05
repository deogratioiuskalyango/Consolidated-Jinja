<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSelectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the role picker page.
     */
    public function index()
    {
        $user = auth()->user();

        // Primary role slug from users.role integer
        $primarySlug = $this->roleSlugs()[$user->role] ?? null;

        // Additional roles from system_user_roles table
        $additionalSlugs = SystemUserRole::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('role_slug')
            ->toArray();

        // Merge, deduplicate, and filter out roles whose account records are missing/inactive
        $allSlugs = collect(array_unique(array_merge(
            $primarySlug ? [$primarySlug] : [],
            $additionalSlugs
        )))->filter(fn($slug) => $this->canAccessRole($user, $slug))->values();

        // Enrich each slug with its metadata
        $roles = $allSlugs->map(fn($slug) => array_merge(
            SystemUserRole::getRoleMeta($slug),
            ['slug' => $slug]
        ))->values();

        // If the user only has one role, skip the picker and go straight to that dashboard
        if ($roles->count() == 1) {
            return $this->switchTo($roles[0]['slug']);
        }

        return view('auth.role-select', [
            'roles'      => $roles,
            'activeRole' => session('active_role'),
            'user'       => $user,
        ]);
    }

    /**
     * Switch the active role and redirect to its dashboard.
     */
    public function switchTo(string $slug)
    {
        $user = auth()->user();

        $primarySlug = $this->roleSlugs()[$user->role] ?? null;

        $additionalSlugs = SystemUserRole::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('role_slug')
            ->toArray();

        $allSlugs = collect(array_unique(array_merge(
            $primarySlug ? [$primarySlug] : [],
            $additionalSlugs
        )))->filter(fn($s) => $this->canAccessRole($user, $s))->values()->all();

        if (!in_array($slug, $allSlugs)) {
            if ($slug === 'shareholder') {
                $message = 'Your shareholder account has not been set up by an admin.';
            } elseif ($slug === 'accountant') {
                $message = 'Your accountant account has not been set up by an admin.';
            } else {
                $message = 'You do not have access to this role.';
            }
            return redirect()->route('role.select.index')->with('error', $message);
        }

        session()->forget('active_role_owner_user_id');

        $assignment = SystemUserRole::where('user_id', $user->id)
            ->where('role_slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($assignment?->owner_user_id) {
            session(['active_role_owner_user_id' => $assignment->owner_user_id]);
        }

        session(['active_role' => $slug]);

        $meta = SystemUserRole::getRoleMeta($slug);

        try {
            return redirect()->route($meta['dashboard']);
        } catch (\Exception $e) {
            return redirect()->route('owner.dashboard');
        }
    }

    /**
     * Handle POST submission from the role picker form.
     */
    public function select(Request $request)
    {
        $request->validate(['role' => 'required|string']);

        return $this->switchTo($request->role);
    }

    /**
     * Determine whether the given user can access a role slug.
     * Shareholder and accountant require an active account record;
     * all other roles are available as long as they are assigned.
     */
    private function canAccessRole(\App\Models\User $user, string $slug): bool
    {
        if ($slug === 'shareholder') {
            return $user->shareholder && $user->shareholder->status == SHAREHOLDER_STATUS_ACTIVE;
        }
        if ($slug === 'accountant') {
            return $user->accountant && $user->accountant->status == ACCOUNTANT_STATUS_ACTIVE;
        }
        return true;
    }

    /**
     * Map users.role integer to a role slug.
     * Delegates to SystemUserRole::primaryRoleSlugMap() — single source of truth.
     */
    private function roleSlugs(): array
    {
        return SystemUserRole::primaryRoleSlugMap();
    }
}
