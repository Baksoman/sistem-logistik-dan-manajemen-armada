<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Services\RolePermissionService;
use App\Http\Requests\Role\UpdateRolePermissionRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\StorePermissionRequest;

class RolePermissionController extends Controller
{
    public function __construct(protected RolePermissionService $rolePermissionService) {}

    public function index(Request $request)
    {
        $roles = $this->rolePermissionService->getAllRoles();
        $permissions = $this->rolePermissionService->getAllPermissions();
        
        $selectedRole = null;
        if ($request->has('role')) {
            $selectedRole = $this->rolePermissionService->getRoleWithPermissions($request->role);
        } elseif ($roles->isNotEmpty()) {
            $selectedRole = $this->rolePermissionService->getFirstRoleWithPermissions();
        }

        return view('rbac.index', compact('roles', 'permissions', 'selectedRole'));
    }

    public function update(UpdateRolePermissionRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $this->rolePermissionService->syncRolePermissions(
            $role, 
            $request->validated('permissions', [])
        );

        return back()->with('success', 'Permissions updated successfully for role: ' . $role->name);
    }

    public function storeRole(StoreRoleRequest $request)
    {
        $this->rolePermissionService->createRole($request->validated());
        return back()->with('success', 'New role created successfully.');
    }

    public function storePermission(StorePermissionRequest $request)
    {
        $this->rolePermissionService->createPermission($request->validated());
        return back()->with('success', 'New permission created successfully.');
    }
}
