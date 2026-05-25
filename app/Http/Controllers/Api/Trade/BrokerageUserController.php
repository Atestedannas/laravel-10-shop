<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrokerageUserController extends Controller
{
    /**
     * 缁戝畾鍒嗛攢鍏崇郴
     */
    public function bind(Request $request)
    {
        $user = auth('sanctum')->user();
        $brokerageUserId = $request->input('brokerage_user_id');

        // 妯℃嫙瀹炵幇
        return api_success(null, '缁戝畾鎴愬姛');
    }

    /**
     * 鑾峰彇褰撳墠鍒嗛攢鐢ㄦ埛
     */
    public function get()
    {
        $user = auth('sanctum')->user();

        return api_success([
            'user_id' => $user->id,
            'level' => 1,
            'brokerage_price' => 0,
            'frozen_price' => 0,
            'user_count' => 0,
            'order_count' => 0,
        ]);
    }

    /**
     * 鍒嗛攢姹囨€?     */
    public function getSummary()
    {
        $user = auth('sanctum')->user();

        return api_success([
            'today_brokerage_price' => 0,
            'yesterday_brokerage_price' => 0,
            'total_brokerage_price' => 0,
            'frozen_brokerage_price' => 0,
            'user_count' => 0,
        ]);
    }

    /**
     * 鎺掕-鎸変剑閲?     */
    public function getRankByPrice()
    {
        return api_success(null);
    }

    /**
     * 鎺掕鍒嗛〉-鎸変剑閲?     */
    public function rankPageByPrice(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }

    /**
     * 鎺掕鍒嗛〉-鎸夌敤鎴锋暟
     */
    public function rankPageByUserCount(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }

    /**
     * 涓嬬骇鐢ㄦ埛鍒嗛〉
     */
    public function childSummaryPage(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }
}