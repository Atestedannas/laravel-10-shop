<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permissionSlug  从路由传入的权限标识，如 'edit-product'
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissionSlug)
    {
        // 1. 获取当前已认证的用户（后台管理员）
        $user = Auth::user();

        // 2. 如果未登录，重定向到登录页（可根据需要调整）
        if (!$user) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => '未登录',
                'data' => null,
            ], 401);
        }

        // 3. 超级管理员可以跳过所有权限检查（可选）
        if ((bool) ($user->is_admin ?? false) || $user->hasRole('super-admin')) {
            return $next($request);
        }

        // 4. 检查用户是否拥有指定的权限
        if (!$user->hasPermission($permissionSlug)) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => '您没有权限执行此操作。',
                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}
