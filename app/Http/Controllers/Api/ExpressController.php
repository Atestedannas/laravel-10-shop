<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ExpressController extends Controller
{
    /**
     * 物流公司列表
     * 杩斿洖 hardcode 常用物流公司数据
     */
    public function list()
    {
        $expressList = [
            ['express_id' => 1, 'express_name' => '椤轰赴閫熻繍'],
            ['express_id' => 2, 'express_name' => '圆通快递],
            ['express_id' => 3, 'express_name' => '申通快递],
            ['express_id' => 4, 'express_name' => '中通快递],
            ['express_id' => 5, 'express_name' => '韵达快递],
        ];

        return api_response($expressList);
    }
}