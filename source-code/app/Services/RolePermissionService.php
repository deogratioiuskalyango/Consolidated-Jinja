<?php

namespace App\Services;

use App\Models\SystemUserRole;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    use ResponseTrait;

    public function getRoleList($request)
    {
        $roles = Role::where('user_id',getOwnerUserId())->orderBy('created_at','DESC')->get();

        return datatables($roles)
            ->addIndexColumn()
            ->editColumn('status', function ($role) {
                if ($role->status == ACTIVE) {
                    return '<div class="status-btn status-btn-green font-13 radius-4">'.__('Active').'</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">'.__('Deactivate').'</div>';
                }
            })
            ->addColumn('action', function ($role) {
                return '<div class="tbl-action-btns d-inline-flex text-end">
                <button type="button" class="p-1 tbl-action-btn edit" data-id="' . $role->id . '" title="' . __('Edit') . '">
                    <span class="iconify" data-icon="clarity:note-edit-solid"></span>
                </button>
                <button onclick="deleteItem(\'' . route('owner.role-permission.delete', $role->id) . '\', \'roleListDataTable\')" class="p-1 tbl-action-btn" title="' . __('Delete') . '">
                    <span class="iconify" data-icon="ep:delete-filled"></span>
                </button>
                <button type="button" onclick="getEditModal(\'' . route('owner.role-permission.permission', $role->id) . '\', \'#permissionModal\')" class="p-1 tbl-action-btn reminder" title="' . __('Add Permission') . '">
                    <span class="iconify" data-icon="ri:send-plane-fill"></span>
                </button>
            </div>';
            })
            ->rawColumns(['action','status'])
            ->make(true);
    }

    public function getInfo($id){

        return Role::findOrFail($id);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            if ($request->id) {
                $role = Role::find($request->id);
            } else {
                $role = new Role();
            }
            $role->name = $request->name.'-'.getOwnerUserId();
            $role->display_name = $request->name;
            $role->guard_name = 'web';
            $role->user_id = getOwnerUserId();
            $role->status = $request->status;
            $role->save();

            DB::commit();
            $message = $request->id ? __(UPDATED_SUCCESSFULLY) : __(CREATED_SUCCESSFULLY);
            return $this->success([], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }

    public function delete($id)
    {
        try {
            $role = Role::find($id);
            $role->delete();

            return $this->success([], __(DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }

    public function updatePermission($request)
    {
        try {
            DB::beginTransaction();
            $role = Role::where('id', $request->role_id)->first();
            $role->syncPermissions($request->permissions);

            DB::commit();
            $message = __(UPDATED_SUCCESSFULLY);
            return $this->success([], $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }

    }

    public function getUserAssignmentList($request) {
        $assignments = \App\Models\SystemUserRole::where('owner_user_id', getOwnerUserId())
            ->with('user')
            ->get();

        return datatables($assignments)
            ->addIndexColumn()
            ->addColumn('user_name', fn($a) => trim(optional($a->user)->first_name . ' ' . optional($a->user)->last_name) ?: optional($a->user)->email ?? '-')
            ->addColumn('user_email', fn($a) => optional($a->user)->email ?? '-')
            ->addColumn('role_label', fn($a) => \App\Models\SystemUserRole::getRoleMeta($a->role_slug)['label'] ?? $a->role_slug)
            ->addColumn('status_badge', fn($a) => $a->is_active
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Revoked</span>')
            ->addColumn('assigned_date', fn($a) => $a->assigned_at ? $a->assigned_at->format('d M Y') : $a->created_at->format('d M Y'))
            ->addColumn('action', fn($a) => $a->is_active
                ? '<button class="btn btn-sm btn-outline-danger revoke-role-btn" data-user-id="'.$a->user_id.'" data-slug="'.$a->role_slug.'" title="Revoke"><i class="ri-close-circle-line"></i> Revoke</button>'
                : '<span class="text-muted small">Revoked</span>')
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function assignRole($request) {
        try {
            $ownerUserId = getOwnerUserId();
            $existing = \App\Models\SystemUserRole::where('user_id', $request->user_id)
                ->where('role_slug', $request->role_slug)
                ->where('owner_user_id', $ownerUserId)
                ->first();

            if ($existing) {
                if ($existing->is_active) {
                    return $this->error([], 'This role is already assigned to this user.');
                }
                $existing->update(['is_active' => true, 'removed_at' => null, 'assigned_at' => now(), 'notes' => $request->notes]);
            } else {
                \App\Models\SystemUserRole::create([
                    'user_id'        => $request->user_id,
                    'role_slug'      => $request->role_slug,
                    'owner_user_id'  => $ownerUserId,
                    'assigned_by'    => auth()->id(),
                    'is_active'      => true,
                    'notes'          => $request->notes,
                    'assigned_at'    => now(),
                ]);
            }
            return $this->success([], 'Role assigned successfully.');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function revokeRole($request) {
        try {
            $ownerUserId = getOwnerUserId();
            \App\Models\SystemUserRole::where('user_id', $request->user_id)
                ->where('role_slug', $request->role_slug)
                ->where('owner_user_id', $ownerUserId)
                ->update(['is_active' => false, 'removed_at' => now()]);
            return $this->success([], 'Role revoked successfully.');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}
