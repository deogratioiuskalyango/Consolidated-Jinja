<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SystemUserRole;
use App\Models\User;
use App\Services\RolePermissionService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    use ResponseTrait;

    public $rolePermissionService;

    public function __construct()
    {
        $this->rolePermissionService = new RolePermissionService();
    }

    public function getRoleData(Request $request)
    {
        if ($request->ajax()) {
            return $this->rolePermissionService->getRoleList($request);
        } else {
            $data['pageTitle'] = __('Role & Permission');
            return view('owner.role_permission.role', $data);
        }
    }

    public function getInfo(Request $request){

        $data = $this->rolePermissionService->getInfo($request->id);
        return $this->success($data);

    }

    public function store(Request $request){

        $request->validate([
            'name' => [
                'required',
                Rule::unique('roles', 'display_name')
                    ->where('user_id', getOwnerUserId())
                    ->ignore($request->id),
            ],
        ]);
        return $this->rolePermissionService->store($request);
    }

    public function delete($id){

        return $this->rolePermissionService->delete($id);
    }

    public function permission($id)
    {
        $data['role'] = Role::findOrFail($id);
        $data['permissions'] = Permission::all();
        $data['oldPermissions'] = $data['role']->permissions->pluck('name')->toArray();
        return view('owner.role_permission.permission')->with($data);
    }

    public function permissionUpdate(Request $request)
    {
        return $this->rolePermissionService->updatePermission($request);
    }

    // User Role Assignments
    public function userAssignments(Request $request) {
        if ($request->ajax()) {
            return $this->rolePermissionService->getUserAssignmentList($request);
        }
        // Shouldn't be called non-AJAX but handle gracefully
        return redirect()->route('owner.role-permission.role-list');
    }

    public function assignRole(Request $request) {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'role_slug' => 'required|string',
            'notes'     => 'nullable|string|max:500',
        ]);
        return $this->rolePermissionService->assignRole($request);
    }

    public function revokeRole(Request $request) {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'role_slug' => 'required|string',
        ]);
        return $this->rolePermissionService->revokeRole($request);
    }

    public function getUsersForAssignment() {
        // Return users for the assign-role modal dropdown (all non-admin users belonging to this owner)
        $users = \App\Models\User::where(function($q) {
                $q->where('owner_user_id', getOwnerUserId())
                  ->orWhere('id', getOwnerUserId());
            })
            ->select('id','first_name','last_name','email','role')
            ->get()
            ->map(fn($u) => [
                'id'    => $u->id,
                'name'  => trim($u->first_name . ' ' . $u->last_name) ?: $u->email,
                'email' => $u->email,
                'role'  => $u->role,
            ]);
        return $this->success(['users' => $users]);
    }
}
