<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * 鑾峰彇浜ゆ槗閰嶇疆
     */
    public function get()
    {
        return api_success([
            'bargin_enable' => true,
            'bargin_title' => '鐮嶄环娲诲姩',
            'seckill_enable' => true,
            'seckill_title' => '闄愭椂绉掓潃',
            'combination_enable' => true,
            'combination_title' => '鎷煎洟娲诲姩',
            'point_enable' => true,
            'point_title' => '绉垎鍟嗗煄',
        ]);
    }
}