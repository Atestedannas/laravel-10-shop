<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    /**
     * 鍥惧舰楠岃瘉鐮侊紙鏆傝繑鍥炵┖鍗犱綅锛?     * 杩斿洖: { captchaId, captchaUrl }
     */
    public function image()
    {
        return api_response([
            'captchaId'  => 'mock',
            'captchaUrl' => '',
        ]);
    }

    /**
     * 鍙戦€佺煭淇￠獙璇佺爜锛堟殏鐩存帴杩斿洖鎴愬姛锛?     */
    public function sendSmsCaptcha(Request $request)
    {
        return api_response(null, '鍙戦€佹垚鍔?);
    }
}