<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * New permissions to create.
     */
    private array $permissions = [
        'View Governance',
        'Manage Governance',
        'View Shareholders',
        'Manage Shareholders',
        'Vote on Resolutions',
        'Manage Resolutions',
        'View Dividends',
        'Manage Dividends',
        'View Financial Approvals',
        'Manage Financial Approvals',
        'View Meetings',
        'Manage Meetings',
        'Manage Accountants',
        'View Rent Collections',
        'Manage Rent Collections',
        'View Financial Reports',
        'Manage Financial Reports',
        'Manage Reconciliation',
        'View Audit Logs',
        'Manage Audit Logs',
        'Manage Roles',
        'Assign Roles',
        'View Directors',
        'Manage Directors',
    ];

    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', $this->permissions)
            ->where('guard_name', 'web')
            ->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
