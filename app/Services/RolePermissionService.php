<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionService
{
    public function getAllRoles(): Collection
    {
        return Role::orderBy('name')->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::orderBy('name')->get();
    }

    public function getRoleWithPermissions(string $roleId): ?Role
    {
        return Role::with('permissions')->findOrFail($roleId);
    }

    public function getFirstRoleWithPermissions(): ?Role
    {
        return Role::with('permissions')->first();
    }

    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
    }

    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    public function createPermission(array $data): Permission
    {
        return Permission::create($data);
    }
}
