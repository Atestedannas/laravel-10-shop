<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use function App\Http\Helpers\api_response;

class UserRoleController extends Controller
{
    public function getInfo(Request $request)
    {

        $user = $request->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }
        $user->load('roles.permissions');
        // 1. 权限码列表：去重后取 permissions.name
        $codes = $user->roles
            ->flatMap(fn($role) => $role->permissions)
            ->unique('id')
            ->pluck('name')
            ->values()
            ->toArray();

        // 2. 角色名称列表
        $roles = $user->roles->pluck('name')->toArray();

        // 3. 动态路由菜单（示例：基于权限动态生成）
        // 实际开发中可存数据库，或根据 codes 判断是否显示菜单项
        $routers = $this->buildRouters($codes);

        // 4. 用户详细信息（根据你的 users 表字段）
        $userData = [
            'id' => $user->id,
            'username' => $user->name,          // 假设 name 相当于用户名
            'real_name' => $user->real_name ?? $user->name,
            'avatar' => $user->avatar ?? '',
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'backend_setting' => $user->backend_setting ?? '{"mode":"light"}',
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ];

        // 5. 最终返回格式
        return response()->json([
            'success' => true,
            'data' => [
                'codes'   => $codes,
                'roles'   => $roles,
                'routers' => $routers,
                'user'    => $userData,
                'menus'   => [],  // 如果你有菜单表，可以额外返回
            ],
            'message' => '获取成功',
        ]);


    }











    /**
     * Get roles of a specific user
     * GET /users/{userId}/roles
     */
    public function userRolesList($userId)
    {
        $user = User::with('roles')->find($userId);
        if (!$user) {
            return api_response(null, 'User not found', 404);
        }
        return api_response($user->roles);
    }

    /**
     * Get permissions of a specific user (deduplicated via roles)
     * GET /users/{userId}/permissions
     */
    public function userPermissionsList($userId)
    {
        $user = User::with('roles.permissions')->find($userId);
        if (!$user) {
            return api_response(null, 'User not found', 404);
        }
        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();

        return api_response($permissions);
    }

    /**
     * Assign one or more roles to a user
     * POST /users/{userId}/roles
     * Body: {"role_ids": [1, 2, 3]}
     */
    public function assignRolesToUsers(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return api_response(null, 'User not found', 404);
        }

        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user->roles()->syncWithoutDetaching($validated['role_ids']);
        $user->load('roles');
        return api_response($user->roles, 'Roles assigned successfully');
    }

    /**
     * Cancel (remove) a role from a user
     * DELETE /users/{userId}/roles/{roleId}
     */
    public function cancleUserRole($userId, $roleId)
    {
        $user = User::find($userId);
        if (!$user) {
            return api_response(null, 'User not found', 404);
        }

        $role = Role::find($roleId);
        if (!$role) {
            return api_response(null, 'Role not found', 404);
        }

        if (!$user->roles()->where('role_id', $roleId)->exists()) {
            return api_response(null, 'User does not have this role', 404);
        }

        $user->roles()->detach($roleId);
        $user->load('roles');
        return api_response($user->roles, 'Role revoked successfully');
    }

    /**
     * Check if a user has a specific role
     * GET /users/{userId}/check-role?role_id=1
     */
    public function checkUserRole(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return api_response(null, 'User not found', 404);
        }

        $roleId = $request->input('role_id');
        if (!$roleId) {
            return api_response(null, 'Missing role_id parameter', 422);
        }

        $hasRole = $user->roles()->where('role_id', $roleId)->exists();
        return api_response(['has_role' => $hasRole]);
    }

    /**
     * Get roles of the currently authenticated user
     * GET /api/user/roles
     */
    public function currentUserRoles()
    {
        $user = auth()->user();
        if (!$user) {
            return api_response(null, 'Not logged in', 401);
        }
        $user->load('roles');
        return api_response($user->roles);
    }

    /**
     * Get permissions of the currently authenticated user
     * GET /api/user/permissions
     */
    public function currentUserPermissions()
    {
        $user = auth()->user();
        if (!$user) {
            return api_response(null, 'Not logged in', 401);
        }
        $user->load('roles.permissions');
        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();
        return api_response($permissions);
    }

    /**
     * Get the currently authenticated user's information (with roles & permissions)
     * GET /api/user/info
     */
    public function currentUserInfo()
    {
        $user = auth()->user();
        if (!$user) {
            return api_response(null, 'Not logged in', 401);
        }
        $user->load('roles.permissions');
        return api_response($user);
    }
}
