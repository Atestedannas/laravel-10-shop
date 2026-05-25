<?php

namespace App\Http\Helpers;

if (!function_exists('api_response')) {
    /**
     * 统一 API 响应（兼容旧版，同时保留 message 和 msg 字段）
     * 
     * @param mixed  $data    业务数据
     * @param string $message 提示信息
     * @param int    $code    业务状态码，0=成功
     */
    function api_response($data = null, string $message = 'success', int $code = 0)
    {
        return response()->json([
            'code'    => $code,
            'message' => $message,
            'msg'     => $message,
            'data'    => $data,
        ], 200);
    }
}

if (!function_exists('api_response_success')) {
    /**
     * 成功响应
     * 
     * @param mixed  $data 业务数据
     * @param string $msg  提示信息
     */
    function api_response_success($data = null, string $msg = '')
    {
        return response()->json([
            'code' => 0,
            'msg'  => $msg ?: 'success',
            'data' => $data,
        ], 200);
    }
}

if (!function_exists('api_response_error')) {
    /**
     * 失败/错误响应
     * 
     * @param int    $code 业务错误码（非 0）
     * @param string $msg  错误提示信息
     */
    function api_response_error(int $code, string $msg = '')
    {
        return response()->json([
            'code' => $code,
            'msg'  => $msg ?: 'error',
            'data' => null,
        ], 200);
    }
}