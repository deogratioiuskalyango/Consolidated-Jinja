<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shareholder;
use App\Models\ShareClass;
use App\Models\GovernanceAuditLog;
use App\Models\SystemUserRole;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use DataTables;

class ShareholderManagementController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $data['pageTitle'] = __('Shareholders');
        $data['shareClasses'] = ShareClass::where('status', ACTIVE)->get();
        $data['availableRoles'] = $this->availableShareholderRoles();
        return view('admin.shareholders.index', $data);
    }

    public function getData()
    {
        try {
            $shareholders = Shareholder::query()
                ->with(['user.systemRoles', 'shareClass'])
                ->leftJoin('users', 'shareholders.user_id', '=', 'users.id')
                ->leftJoin('share_classes', 'shareholders.share_class_id', '=', 'share_classes.id')
                ->select('shareholders.*');

            return DataTables::eloquent($shareholders)
                ->addIndexColumn()
                ->addColumn('name', fn($s) => trim(($s->user->first_name ?? '') . ' ' . ($s->user->last_name ?? '')) ?: '-')
                ->addColumn('email', fn($s) => $s->user->email ?? '-')
                ->addColumn('phone', fn($s) => $s->user->contact_number ?? '-')
                ->addColumn('share_class', fn($s) => $s->shareClass->name ?? '-')
                ->addColumn('shares', fn($s) => number_format($s->total_shares, 2))
                ->addColumn('percentage', fn($s) => $s->ownership_percentage . '%')
                ->addColumn('roles', fn($s) => $this->roleBadgesHtml($s))
                ->addColumn('status_badge', fn($s) => $s->status == SHAREHOLDER_STATUS_ACTIVE
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Suspended</span>')
                ->addColumn('action', fn($s) => view('admin.shareholders.action', ['shareholder' => $s])->render())
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('users.first_name', 'like', "%{$keyword}%")
                            ->orWhere('users.last_name', 'like', "%{$keyword}%")
                            ->orWhereRaw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) LIKE ?", ["%{$keyword}%"]);
                    });
                })
                ->orderColumn('name', 'users.first_name $1, users.last_name $1')
                ->rawColumns(['roles', 'status_badge', 'action'])
                ->make(true);
        } catch (\Throwable $e) {
            \Log::error('Shareholder DataTables failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'draw' => (int) request('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => __('Unable to load shareholders. Please refresh and try again.'),
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|max:50',
            'share_class_id' => 'nullable|exists:share_classes,id',
            'shares_held' => 'required|numeric|min:0',
            'fixed_voting_weight' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $password = \Str::random(10);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_number' => $request->phone,
                'password' => Hash::make($password),
                'role' => USER_ROLE_SHAREHOLDER,
                'status' => USER_STATUS_ACTIVE,
            ]);

            $totalIssued = Shareholder::where('share_class_id', $request->share_class_id)->sum('total_shares');
            $sharesHeld = (float) $request->shares_held;
            $ownership = $totalIssued > 0 ? round(($sharesHeld / ($totalIssued + $sharesHeld)) * 100, 4) : 100;

            $count = Shareholder::count() + 1;
            Shareholder::create([
                'user_id' => $user->id,
                'owner_user_id' => auth()->id(),
                'share_class_id' => $request->share_class_id,
                'shareholder_id' => 'SH-' . str_pad($count, 4, '0', STR_PAD_LEFT),
                'total_shares' => $sharesHeld,
                'ownership_percentage' => $ownership,
                'fixed_voting_weight' => $request->fixed_voting_weight ?? 1,
                'date_joined' => now()->toDateString(),
                'status' => SHAREHOLDER_STATUS_ACTIVE,
                'force_password_change' => true,
            ]);

            try {
                \Mail::mailer('failover')->to($user->email)->send(new \App\Mail\ShareholderCredentialsMail($user, $password));
            } catch (\Throwable $e) {
                \Log::warning('ShareholderCredentialsMail failed for ' . $user->email . ': ' . $e->getMessage());
            }

            DB::commit();
            return $this->success([], __('Shareholder created successfully. Credentials sent to ') . $user->email);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function edit(Shareholder $shareholder)
    {
        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $shareholder->id,
                'first_name' => $shareholder->user->first_name ?? '',
                'last_name' => $shareholder->user->last_name ?? '',
                'email' => $shareholder->user->email ?? '',
                'phone' => $shareholder->user->contact_number ?? '',
                'share_class_id' => $shareholder->share_class_id,
                'total_shares' => $shareholder->total_shares,
                'fixed_voting_weight' => $shareholder->fixed_voting_weight,
            ],
        ]);
    }

    public function update(Request $request, Shareholder $shareholder)
    {
        $request->validate([
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'email' => 'required|email|unique:users,email,' . $shareholder->user_id,
            'phone' => 'nullable|max:50',
            'share_class_id' => 'nullable|exists:share_classes,id',
            'total_shares' => 'required|numeric|min:0',
            'fixed_voting_weight' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $shareholder->user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_number' => $request->phone,
            ]);

            $otherShares = Shareholder::where('share_class_id', $request->share_class_id)
                ->where('id', '!=', $shareholder->id)
                ->sum('total_shares');
            $sharesHeld = (float) $request->total_shares;
            $ownership = ($otherShares + $sharesHeld) > 0
                ? round(($sharesHeld / ($otherShares + $sharesHeld)) * 100, 4)
                : 100;

            $shareholder->update([
                'share_class_id' => $request->share_class_id,
                'total_shares' => $sharesHeld,
                'ownership_percentage' => $ownership,
                'fixed_voting_weight' => $request->fixed_voting_weight ?? 1,
            ]);

            GovernanceAuditLog::record('update_shareholder', $shareholder, [], 'Shareholder information updated');
            DB::commit();
            return $this->success([], __('Shareholder updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function roles(Shareholder $shareholder)
    {
        $shareholder->load('user.systemRoles');
        $assigned = $shareholder->user->systemRoles
            ->where('is_active', true)
            ->pluck('role_slug')
            ->values()
            ->all();

        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $shareholder->id,
                'name' => $shareholder->full_name ?: ($shareholder->user->email ?? __('Shareholder')),
                'email' => $shareholder->user->email ?? '',
                'primary_role' => SystemUserRole::getRoleMeta('shareholder'),
                'assigned_roles' => $assigned,
                'roles' => $this->availableShareholderRoles(),
            ],
        ]);
    }

    public function updateRoles(Request $request, Shareholder $shareholder)
    {
        $allowedSlugs = array_keys($this->availableShareholderRoles());

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'string|in:' . implode(',', $allowedSlugs),
            'notes' => 'nullable|string|max:1000',
        ]);

        $selected = collect($validated['roles'] ?? [])->unique()->values()->all();
        $user = $shareholder->user;

        // Capture previous roles BEFORE making any changes (used for audit diff).
        $previousRoles = SystemUserRole::where('user_id', $user->id)
            ->where('owner_user_id', $shareholder->owner_user_id)
            ->where('is_active', true)
            ->pluck('role_slug')
            ->all();

        DB::beginTransaction();
        try {
            $rolesToRemove = SystemUserRole::where('user_id', $user->id)
                ->where('owner_user_id', $shareholder->owner_user_id)
                ->whereIn('role_slug', $allowedSlugs);

            if (!empty($selected)) {
                $rolesToRemove->whereNotIn('role_slug', $selected);
            }

            $rolesToRemove->update([
                'is_active' => false,
                'removed_at' => now(),
            ]);

            foreach ($selected as $slug) {
                SystemUserRole::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'role_slug' => $slug,
                        'owner_user_id' => $shareholder->owner_user_id,
                    ],
                    [
                        'assigned_by' => auth()->id(),
                        'is_active' => true,
                        'notes' => $validated['notes'] ?? null,
                        'assigned_at' => now(),
                        'removed_at' => null,
                    ]
                );
            }

            GovernanceAuditLog::record('assign_shareholder_roles', $shareholder, [
                'old' => ['roles' => $previousRoles],
                'new' => ['roles' => $selected],
            ], 'Shareholder system roles updated');

            DB::commit();
            return $this->success([
                'roles_html' => $this->roleBadgesHtml($shareholder->fresh(['user.systemRoles'])),
            ], __('Shareholder roles updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function destroy(Shareholder $shareholder)
    {
        DB::beginTransaction();
        try {
            GovernanceAuditLog::record('delete_shareholder', $shareholder, [], 'Shareholder account deleted');
            $shareholder->user->delete();
            $shareholder->delete();
            DB::commit();
            return $this->success([], __('Shareholder deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function suspend(Shareholder $shareholder)
    {
        $shareholder->update(['status' => SHAREHOLDER_STATUS_SUSPENDED]);
        GovernanceAuditLog::record('suspend_shareholder', $shareholder, [], 'Shareholder account suspended');
        return $this->success([], __('Shareholder suspended.'));
    }

    public function reactivate(Shareholder $shareholder)
    {
        $shareholder->update(['status' => SHAREHOLDER_STATUS_ACTIVE]);
        GovernanceAuditLog::record('reactivate_shareholder', $shareholder, [], 'Shareholder account reactivated');
        return $this->success([], __('Shareholder reactivated.'));
    }

    public function resendCredentials(Shareholder $shareholder)
    {
        try {
            $password = \Str::random(10);
            $shareholder->user->update([
                'password' => Hash::make($password),
            ]);
            $shareholder->update(['force_password_change' => true]);

            \Mail::to($shareholder->user->email)
                ->send(new \App\Mail\ShareholderCredentialsMail($shareholder->user, $password));

            GovernanceAuditLog::record('resend_credentials', $shareholder, [], 'Credentials resent to ' . $shareholder->user->email);
            return $this->success([], __('Credentials resent to ') . $shareholder->user->email);
        } catch (\Throwable $e) {
            \Log::error('resendCredentials failed for shareholder #' . $shareholder->id . ': ' . $e->getMessage());
            return $this->error([], __('Failed to send email: ') . $e->getMessage());
        }
    }

    private function availableShareholderRoles(): array
    {
        // Exclude:
        //   'shareholder' — already the user's primary identity in this portal
        //   'accountant'  — separate primary-role portal; not a secondary concept
        //   'owner'       — property-manager role; not meaningful as a shareholder add-on
        // Note: 'admin', 'tenant', 'maintainer' are already absent from allRoleSlugs().
        return collect(SystemUserRole::allRoleSlugs())
            ->except(['shareholder', 'accountant', 'owner'])
            ->all();
    }

    private function roleBadgesHtml(Shareholder $shareholder): string
    {
        if (!$shareholder->relationLoaded('user')) {
            $shareholder->load('user.systemRoles');
        } elseif ($shareholder->user && !$shareholder->user->relationLoaded('systemRoles')) {
            $shareholder->user->load('systemRoles');
        }

        $badges = ['<span class="badge bg-info me-1 mb-1">Shareholder</span>'];
        $activeRoles = $shareholder->user?->systemRoles?->where('is_active', true) ?? collect();

        foreach ($activeRoles as $role) {
            if (!array_key_exists($role->role_slug, $this->availableShareholderRoles())) {
                continue;
            }
            $meta = SystemUserRole::getRoleMeta($role->role_slug);
            $label = e($meta['label']);
            $color = e($meta['color'] ?? 'secondary');
            $badges[] = '<span class="badge bg-' . $color . ' me-1 mb-1">' . $label . '</span>';
        }

        return '<div class="d-flex flex-wrap gap-1 shareholder-role-badges">' . implode('', $badges) . '</div>';
    }
}
