<?php

namespace App\Http\Helpers;

if (!function_exists('api_response')) {
    /**
     * 统一 API 响应（兼容 uniapp 前端格式：code/msg/data）
     *
     * @param mixed  $data    业务数据
     * @param string $message 提示信息
     * @param int    $code    业务状态码，0=成功
     */
    function api_response($data = null, string $message = 'success', int $code = 0)
    {
        return response()->json([
            'code' => $code,
            'msg'  => $message,
            'data' => $data,
        ], 200);
    }
}

if (!function_exists('api_success')) {
    function api_success($data = null, string $msg = 'success')
    {
        return api_response($data, $msg, 0);
    }
}

if (!function_exists('api_error')) {
    function api_error(int $code, string $msg = 'error', $data = null)
    {
        return api_response($data, $msg, $code);
    }
}
