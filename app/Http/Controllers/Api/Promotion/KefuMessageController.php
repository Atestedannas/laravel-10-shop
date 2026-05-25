<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KefuMessageController extends Controller
{
    /**
     * 鍙戦€佸鏈嶆秷鎭?     */
    public function send(Request $request)
    {
        $user = auth('sanctum')->user();
        $content = $request->input('content');
        $type = $request->input('type', 'text');

        // 妯℃嫙鍥炲
        return api_success([
            'id' => rand(10000, 99999),
            'content' => '鎮ㄥソ锛岃闂湁浠€涔堝彲浠ュ府鍔╂偍鐨勶紵',
            'type' => 'text',
            'create_time' => date('Y-m-d H:i:s'),
        ], '鍙戦€佹垚鍔?);
    }

    /**
     * 瀹㈡湇娑堟伅鍒楄〃
     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();

        // 妯℃嫙鏁版嵁
        $messages = [
            [
                'id' => 1,
                'type' => 'text',
                'content' => '娆㈣繋鏉ュ埌鍟嗗煄锛屾湁浠€涔堝彲浠ュ府鍔╂偍鐨勶紵',
                'direction' => 0, // 0=瀹㈡湇鍙戞潵
                'create_time' => date('Y-m-d H:i:s', time() - 3600),
            ],
        ];

        return api_success($messages);
    }
}