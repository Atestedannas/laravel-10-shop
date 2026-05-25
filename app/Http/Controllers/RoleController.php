<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use function App\Http\Helpers\api_response;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return api_response($roles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($validated);

        return api_response($role, '角色创建成功', 201);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return api_response(null, '角色不存在', 404);
        }
        return api_response($role);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return api_response(null, '角色不存在', 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:roles,name,' . $id,
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return api_response($role, '角色更新成功');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return api_response(null, '角色不存在', 404);
        }

        if ($role->id === 1) {
            return api_response(null, '超级管理员角色不可删除', 403);
        }

        $role->delete();

        return api_response(null, '角色删除成功');
    }

    public function grantPermissions(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return api_response(null, '角色不存在', 404);
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions']);

        $role->load('permissions');

        return api_response($role, '权限分配成功');
    }
}
