<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use function App\Http\Helpers\api_response;

class RoleController extends Controller
{
    //

    public function index()
    {
        $roles = Role::all();
        return api_response($roles);

    }

    public function store(Request  $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($validated);

        return api_response($role, '瑙掕壊鍒涘缓鎴愬姛', 201);
    }
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return api_response(null, '瑙掕壊涓嶅瓨鍦?, 404);
        }
        return api_response($role);

    }
    /**
     * 鏇存柊瑙掕壊淇℃伅
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return api_response(null, '瑙掕壊涓嶅瓨鍦?, 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:roles,name,' . $id,
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return api_response($role, '瑙掕壊鏇存柊鎴愬姛');
    }
    /**
     * 鍒犻櫎瑙掕壊
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return api_response(null, '瑙掕壊涓嶅瓨鍦?, 404);
        }

        // 鍙€夛細绂佹鍒犻櫎瓒呯瑙掕壊锛坕d=1锛?        if ($role->id === 1) {
            return api_response(null, '瓒呯骇绠＄悊鍛樿鑹蹭笉鍙垹闄?, 403);
        }

        $role->delete();

        return api_response(null, '瑙掕壊鍒犻櫎鎴愬姛');
    }
    /**
     * 涓鸿鑹插垎閰嶆潈闄愶紙鎺堜簣鏉冮檺锛?     */
    public function grantPermissions(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return api_response(null, '瑙掕壊涓嶅瓨鍦?, 404);
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // sync 浼氬厛绉婚櫎鏃ф潈闄愬啀璧嬩簣鏂版潈闄?        // 濡傛灉闇€瑕佽拷鍔狅紝鍙娇鐢?attach锛涜繖閲屼娇鐢?sync 绗﹀悎甯歌鍒嗛厤閫昏緫
        $role->permissions()->sync($validated['permissions']);

        // 閲嶆柊鍔犺浇鏉冮檺鍏崇郴骞惰繑鍥?        $role->load('permissions');

        return api_response($role, '鏉冮檺鍒嗛厤鎴愬姛');
    }

}
