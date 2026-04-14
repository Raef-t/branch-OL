<?php

namespace Modules\Permissions\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Modules\Users\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RolePermissionService
{
    public function getAllRoles()
    {
        return Role::with('permissions')->get();
    }

    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name']]);
            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('name', $data['permissions'])->pluck('id');
                $role->permissions()->sync($permissions);
            }
            return $role->load('permissions');
        });
    }

    public function updateRole(Role $role, array $data)
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name']]);
            if (isset($data['permissions'])) {
                $permissions = Permission::whereIn('name', $data['permissions'])->pluck('id');
                $role->permissions()->sync($permissions);
            }
            return $role->load('permissions');
        });
    }

    public function deleteRole(Role $role)
    {
        return $role->delete();
    }

    public function assignPermissionsToRole(Role $role, array $permissionNames)
    {
        $permissions = Permission::whereIn('name', $permissionNames)->get();
        $role->syncPermissions($permissions);
        return $role->load('permissions');
    }

    public function getAllPermissions()
    {
        return Permission::all();
    }
    public function assignRoleToUser(int $userId, string $roleName)
    {
        // جلب المستخدم
        $user = User::find($userId);
        if (!$user) {
            throw new \Exception("المستخدم بالمعرف {$userId} غير موجود.");
        }

        // جلب الدور
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            throw new \Exception("الدور '{$roleName}' غير موجود.");
        }

        // تحقق إذا كان المستخدم بالفعل لديه هذا الدور
        if ($user->hasRole($role)) {
            return $user->load('roles'); // إعادة البيانات دون تغيير
        }

        // ربط الدور بالمستخدم
        $user->assignRole($role);

        return $user->load('roles'); // إرجاع المستخدم مع الأدوار بعد الربط
    }
    /**
     * يربط عدة أدوار بمستخدم دفعة واحدة.
     *
     * @param  int   $userId
     * @param  array $roleNames
     * @return \Modules\Users\Models\User
     *
     * @throws ModelNotFoundException إذا لم يجد المستخدم أو أحد الأدوار
     */
    public function assignMultipleRolesToUser(int $userId, array $roleNames): User
    {
        $user = User::findOrFail($userId);

        // تأكد من أن كل دور موجود
        $roles = Role::whereIn('name', $roleNames)->get();
        if (count($roles) !== count($roleNames)) {
            throw new \Exception('أحد الأدوار غير موجودة.');
        }

        // يضيف الأدوار دون حذف الموجود بالفعل
        $user->assignRole($roleNames);

        return $user->load('roles');
    }
    
    public function removeRoleFromUser(int $userId, string $roleName): User
    {
        $user = User::findOrFail($userId);

        if (! $user->hasRole($roleName)) {
            throw new \Exception("المستخدم لا يملك الدور '{$roleName}'.");
        }

        $user->removeRole($roleName);

        return $user->load('roles');
    }

    /**
     * إزالة عدة أدوار من المستخدم دفعة واحدة.
     */
    public function removeMultipleRolesFromUser(int $userId, array $roleNames): User
    {
        $user = User::findOrFail($userId);

        foreach ($roleNames as $roleName) {
            if ($user->hasRole($roleName)) {
                $user->removeRole($roleName);
            }
        }

        return $user->load('roles');
    }
}
