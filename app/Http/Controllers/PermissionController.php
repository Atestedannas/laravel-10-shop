<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use function App\Http\Helpers\api_response;

class PermissionController extends Controller
{
    /**
     * 鑾峰彇鎵€鏈夋潈闄愬垪琛?     */
    public function index()
    {
        $permissions = Permission::all();
        return api_response($permissions);
    }

    /**
     * 鍒涘缓鏂版潈闄?     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($validated);

        return api_response($permission, '鏉冮檺鍒涘缓鎴愬姛', 201);
    }

    /**
     * 鑾峰彇鍗曚釜鏉冮檺璇︽儏
     */
    public function show($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return api_response(null, '鏉冮檺涓嶅瓨鍦?, 404);
        }
        return api_response($permission);
    }

    /**
     * 鏇存柊鏉冮檺淇℃伅
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return api_response(null, '鏉冮檺涓嶅瓨鍦?, 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:permissions,name,' . $id,
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return api_response($permission, '鏉冮檺鏇存柊鎴愬姛');
    }

    /**
     * 鍒犻櫎鏉冮檺
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return api_response(null, '鏉冮檺涓嶅瓨鍦?, 404);
        }

        // 鍙€夛細绂佹鍒犻櫎宸茶瑙掕壊浣跨敤鐨勬潈闄愶紙閬垮厤鑴忔暟鎹級
        if ($permission->roles()->exists()) {
            return api_response(null, '璇ユ潈闄愬凡鍒嗛厤缁欒鑹诧紝涓嶅彲鍒犻櫎', 403);
        }

        $permission->delete();

        return api_response(null, '鏉冮檺鍒犻櫎鎴愬姛');
    }

}
