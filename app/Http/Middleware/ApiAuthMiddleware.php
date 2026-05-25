<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiAuthMiddleware
{
    /**
     * 处理 API 认证
     *
     * 从 Authorization: Bearer <token> 提取 token，
     * 通过 Sanctum personal_access_tokens 表验证并注入用户。
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $this->extractBearerToken($request);

        if (! $token) {
            return $this->unauthorized();
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken || ! $accessToken->tokenable) {
            return $this->unauthorized();
        }

        // 注入认证用户到 request
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        // 同时设置 Laravel 标准 auth guard 用户（方便 auth()->user() 使用）
        auth()->setUser($accessToken->tokenable);

        return $next($request);
    }

    /**
     * 从 Authorization header 提取 Bearer token
     */
    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return substr($header, 7);
    }

    /**
     * 返回未认证响应
     */
    private function unauthorized()
    {
        return response()->json([
            'code' => 401,
            'msg'  => '未登录或token已过期',
            'data' => null,
        ], 200); // 前端约定 HTTP 200 + code=401
    }
}