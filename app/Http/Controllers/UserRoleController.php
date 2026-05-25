<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use function App\Http\Helpers\api_response;

class UserRoleController extends Controller
{
    /**
     * 鑾峰彇鎸囧畾鐢ㄦ埛鐨勮鑹插垪琛?     * GET /users/{userId}/roles
     */
    public function userRolesList($userId)
    {
        $user = User::with('roles')->find($userId);
        if (!$user) {
            return api_response(null, '鐢ㄦ埛涓嶅瓨鍦?, 404);
        }
        return api_response($user->roles);
    }

    /**
     * 鑾峰彇鎸囧畾鐢ㄦ埛鐨勬潈闄愬垪琛紙閫氳繃瑙掕壊鍘婚噸锛?     * GET /users/{userId}/permissions
     */
    public function userPermissionsList($userId)
    {
        $user = User::with('roles.permissions')->find($userId);
        if (!$user) {
            return api_response(null, '鐢ㄦ埛涓嶅瓨鍦?, 404);
        }

        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();

        return api_response($permissions);
    }

    /**
     * 涓虹敤鎴峰垎閰嶄竴涓垨澶氫釜瑙掕壊
     * POST /users/{userId}/roles
     * Body: {"role_ids": [1, 2, 3]}
     */
    public function assignRolesToUsers(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return api_response(null, '鐢ㄦ埛涓嶅瓨鍦?, 404);
        }

        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        // syncWithoutDetaching 浼氶檮鍔犳柊瑙掕壊锛屼笉浼氱Щ闄ゅ凡鏈夎鑹?        $user->roles()->syncWithoutDetaching($validated['role_ids']);

        $user->load('roles');
        return api_response($user->roles, '瑙掕壊鍒嗛厤鎴愬姛');
    }

    /**
     * 鍙栨秷鐢ㄦ埛鐨勬煇涓鑹?     * DELETE /users/{userId}/roles/{roleId}
     */
    public function cancleUserRole($userId, $roleId)
    {
        $user = User::find($userId);
        if (!$user) {
            return api_response(null, '鐢ㄦ埛涓嶅瓨鍦?, 404);
        }

        $role = Role::find($roleId);
        if (!$role) {
            return api_response(null, '瑙掕壊涓嶅瓨鍦?, 404);
        }

        if (!$user->roles()->where('role_id', $roleId)->exists()) {
            return api_response(null, '璇ョ敤鎴锋湭鎷ユ湁姝よ鑹?, 404);
        }

        $user->roles()->detach($roleId);

        $user->load('roles');
        return api_response($user->roles, '瑙掕壊鎾ら攢鎴愬姛');
    }

    /**
     * 妫€鏌ョ敤鎴锋槸鍚︽嫢鏈夋寚瀹氳鑹诧紙鍙€夋鏌ユ潈闄愶級
     * GET /users/{userId}/check-role?role_id=1
     * 鎴栬€?POST 浼犲弬
     */
    public function checkUserRole(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return api_response(null, '鐢ㄦ埛涓嶅瓨鍦?, 404);
        }

        $roleId = $request->input('role_id');
        if (!$roleId) {
            return api_response(null, '璇锋彁渚?role_id 鍙傛暟', 422);
        }

        $hasRole = $user->roles()->where('role_id', $roleId)->exists();
        return api_response(['has_role' => $hasRole]);
    }

    /**
     * 鑾峰彇褰撳墠鐧诲綍鐢ㄦ埛鐨勮鑹插垪琛?     * GET /api/user/roles
     */
    public function currentUserRoles()
    {
        $user = auth()->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }
        $user->load('roles');
        return api_response($user->roles);
    }

    /**
     * 鑾峰彇褰撳墠鐧诲綍鐢ㄦ埛鐨勬潈闄愬垪琛紙閫氳繃瑙掕壊鍚堝苟鍘婚噸锛?     * GET /api/user/permissions
     */
    public function currentUserPermissions()
    {
        $user = auth()->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }
        $user->load('roles.permissions');
        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();
        return api_response($permissions);
    }
}
