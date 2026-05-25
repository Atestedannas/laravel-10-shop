<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * 成功响应
     *
     * @param mixed|null $data 业务数据
     * @param string     $message  提示信息
     * @param int        $httpStatus HTTP 状态码
     * @return JsonResponse
     */
    protected function success($data = null, string $message = 'success', int $httpStatus = 200): JsonResponse
    {
        return response()->json([
            'status'  => 200,
            'message' => $message,
            'data'    => $data,
        ], $httpStatus);
    }

    /**
     * 失败响应
     *
     * @param int        $status  业务状态码（500/401 等）
     * @param string     $message   错误提示信息
     * @param mixed|null $data  附加数据
     * @param int        $httpStatus HTTP 状态码
     * @return JsonResponse
     */
    protected function error(int $status, string $message, $data = null, int $httpStatus = 200): JsonResponse
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ], $httpStatus);
    }
}