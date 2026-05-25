<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use function App\Http\Helpers\api_response;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return api_response($permissions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($validated);

        return api_response($permission, '权限创建成功', 201);
    }

    public function show($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return api_response(null, '权限不存在', 404);
        }
        return api_response($permission);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return api_response(null, '权限不存在', 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:permissions,name,' . $id,
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return api_response($permission, '权限更新成功');
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return api_response(null, '权限不存在', 404);
        }

        if ($permission->roles()->exists()) {
            return api_response(null, '该权限已分配给角色，不可删除', 403);
        }

        $permission->delete();

        return api_response(null, '权限删除成功');
    }
}
