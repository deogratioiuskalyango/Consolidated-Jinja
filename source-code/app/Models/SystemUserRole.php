<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUserRole extends Model
{
    protected $table = 'system_user_roles';

    protected $fillable = [
        'user_id',
        'role_slug',
        'owner_user_id',
        'assigned_by',
        'is_active',
        'notes',
        'assigned_at',
        'removed_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'assigned_at' => 'datetime',
        'removed_at'  => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // ── Role Registry ────────────────────────────────────────────────────────

    /**
     * Canonical map of users.role integer → role slug.
     * Single source of truth — used by LoginController, RoleSelectController, etc.
     *
     * Note: USER_ROLE_TEAM_MEMBER maps to 'owner' because team members share the
     * owner portal but are scoped to their owner_user_id.
     */
    public static function primaryRoleSlugMap(): array
    {
        return [
            USER_ROLE_OWNER       => 'owner',       // 1
            USER_ROLE_TENANT      => 'tenant',      // 2
            USER_ROLE_MAINTAINER  => 'maintainer',  // 3
            USER_ROLE_ADMIN       => 'admin',       // 4
            USER_ROLE_TEAM_MEMBER => 'owner',       // 5
            USER_ROLE_SHAREHOLDER => 'shareholder', // 6
            USER_ROLE_ACCOUNTANT  => 'accountant',  // 7
        ];
    }

    /**
     * All defined secondary/system roles with UI metadata.
     *
     * IMPORTANT: 'admin' is intentionally excluded from this list.
     * Admin access must come from users.role = USER_ROLE_ADMIN only.
     * Including 'admin' here would allow privilege escalation.
     *
     * 'tenant' and 'maintainer' are primary-role-only concepts and should
     * not be assigned as secondary roles.
     */
    public static function allRoleSlugs(): array
    {
        return [
            'owner'              => ['label' => 'Property Manager',     'icon' => 'ri-building-line',        'color' => 'primary',   'dashboard' => 'owner.dashboard'],
            'accountant'         => ['label' => 'Accountant',           'icon' => 'ri-calculator-line',      'color' => 'success',   'dashboard' => 'accountant.dashboard'],
            'shareholder'        => ['label' => 'Shareholder',          'icon' => 'ri-stock-line',           'color' => 'info',      'dashboard' => 'shareholder.dashboard'],
            'director'           => ['label' => 'Director',             'icon' => 'ri-briefcase-4-line',     'color' => 'dark',      'dashboard' => 'role.director.dashboard'],
            'landlord'           => ['label' => 'Landlord',             'icon' => 'ri-home-8-line',          'color' => 'warning',   'dashboard' => 'role.landlord.dashboard'],
            'tenant_manager'     => ['label' => 'Tenant Manager',       'icon' => 'ri-team-line',            'color' => 'primary',   'dashboard' => 'role.tenant-manager.dashboard'],
            'compliance_officer' => ['label' => 'Compliance Officer',   'icon' => 'ri-file-shield-2-line',   'color' => 'info',      'dashboard' => 'role.compliance-officer.dashboard'],
            'secretary'          => ['label' => 'Secretary',            'icon' => 'ri-calendar-todo-line',   'color' => 'success',   'dashboard' => 'role.secretary.dashboard'],
            'auditor'            => ['label' => 'Auditor',              'icon' => 'ri-eye-line',             'color' => 'warning',   'dashboard' => 'role.auditor.dashboard'],
            'finance_manager'    => ['label' => 'Finance Manager',      'icon' => 'ri-money-dollar-box-line','color' => 'danger',    'dashboard' => 'role.finance-manager.dashboard'],
        ];
    }

    /**
     * All role slugs including primary-only roles (admin, tenant, maintainer).
     * Used for display/badge rendering only — not for assignment.
     */
    public static function allRoleSlugsWithPrimary(): array
    {
        return array_merge([
            'admin'      => ['label' => 'System Administrator', 'icon' => 'ri-shield-star-line', 'color' => 'danger',    'dashboard' => 'admin.dashboard'],
            'tenant'     => ['label' => 'Tenant',               'icon' => 'ri-user-line',         'color' => 'secondary', 'dashboard' => 'tenant.dashboard'],
            'maintainer' => ['label' => 'Maintenance Tech',     'icon' => 'ri-tools-line',        'color' => 'secondary', 'dashboard' => 'maintainer.dashboard'],
        ], static::allRoleSlugs());
    }

    /**
     * Retrieve metadata for a single role slug.
     */
    public static function getRoleMeta(string $slug): array
    {
        return static::allRoleSlugsWithPrimary()[$slug] ?? [
            'label'     => ucfirst(str_replace('_', ' ', $slug)),
            'icon'      => 'ri-user-line',
            'color'     => 'secondary',
            'dashboard' => 'owner.dashboard',
        ];
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
