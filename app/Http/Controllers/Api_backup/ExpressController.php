<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ExpressController extends Controller
{
    /**
     * 鐗╂祦鍏徃鍒楄〃
     * 杩斿洖 hardcode 甯哥敤鐗╂祦鍏徃鏁版嵁
     */
    public function list()
    {
        $expressList = [
            ['express_id' => 1, 'express_name' => '椤轰赴閫熻繍'],
            ['express_id' => 2, 'express_name' => '鍦嗛€氬揩閫?],
            ['express_id' => 3, 'express_name' => '涓€氬揩閫?],
            ['express_id' => 4, 'express_name' => '鐢抽€氬揩閫?],
            ['express_id' => 5, 'express_name' => '闊佃揪蹇€?],
        ];

        return api_response($expressList);
    }
}