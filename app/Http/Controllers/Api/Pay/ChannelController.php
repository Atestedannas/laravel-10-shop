<?php

namespace App\Http\Controllers\Api\Pay;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChannelController extends Controller
{

    /**
     * 鑾峰彇鍙敤鏀粯娓犻亾缂栫爜鍒楄〃
     * uniapp: GET /pay/channel/get-enable-code-list?appId=xxx
     */
    public function getEnableCodeList(Request $request)
    {
        $appId = $request->input('appId', '');

        $channels = [
            ['code' => 'weixin_mini', 'name' => '寰俊灏忕▼搴忔敮浠?],
            ['code' => 'wallet', 'name' => '閽卞寘鏀粯'],
            ['code' => 'alipay', 'name' => '鏀粯瀹濇敮浠?],
            ['code' => 'wechat_pay', 'name' => '寰俊鏀粯'],
            ['code' => 'balance', 'name' => '浣欓鏀粯'],
        ];

        return api_success($channels);
    }
}