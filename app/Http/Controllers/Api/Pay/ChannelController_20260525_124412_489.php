<?php

namespace App\Http\Controllers\Api\Pay;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    use ApiResponse;

    /**
     * 获取启用的支付渠道编码列表
     */
    public function getEnableCodeList(Request $request)
    {
        $appId = $request->input('appId', '');

        // 支持的支付渠道编码列表
        $channels = [
            ['code' => 'weixin_mini', 'name' => '微信小程序支付'],
            ['code' => 'wallet', 'name' => '钱包支付'],
            ['code' => 'alipay', 'name' => '支付宝支付'],
        ];

        return $this->success($channels);
    }
}