<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrokerageWithdrawController extends Controller
{
    /**
     * 鍒涘缓鎻愮幇
     */
    public function create(Request $request)
    {
        $user = auth('sanctum')->user();

        return api_success(null, '鎻愮幇鐢宠宸叉彁浜?);
    }

    /**
     * 鎻愮幇鍒嗛〉
     */
    public function page(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }

    /**
     * 鎻愮幇璇︽儏
     */
    public function get(Request $request)
    {
        return api_success(null);
    }
}