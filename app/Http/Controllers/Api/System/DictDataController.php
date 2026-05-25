<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DictDataController extends Controller
{
    /**
     * 鏍规嵁绫诲瀷鑾峰彇瀛楀吀鏁版嵁
     */
    public function type(Request $request)
    {
        $type = $request->input('type');

        // 妯℃嫙鏁版嵁
        $dictMap = [
            'order_status' => [
                ['value' => 0, 'label' => '宸插彇娑?],
                ['value' => 1, 'label' => '寰呬粯娆?],
                ['value' => 2, 'label' => '寰呭彂璐?],
                ['value' => 3, 'label' => '寰呮敹璐?],
                ['value' => 4, 'label' => '宸插畬鎴?],
            ],
            'refund_type' => [
                ['value' => 1, 'label' => '浠呴€€娆?],
                ['value' => 2, 'label' => '閫€璐ч€€娆?],
            ],
            'coupon_type' => [
                ['value' => 1, 'label' => '婊″噺鍒?],
                ['value' => 2, 'label' => '鎶樻墸鍒?],
            ],
        ];

        return api_success($dictMap[$type] ?? []);
    }
}