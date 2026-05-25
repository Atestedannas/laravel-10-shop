<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    /**
     * 图形验证码(暂返回空占位)     * 杩斿洖: { captchaId, captchaUrl }
     */
    public function image()
    {
        return api_response([
            'captchaId'  => 'mock',
            'captchaUrl' => '',
        ]);
    }

    /**
     * 发送短信验证码(暂直接返回成功)     */
    public function sendSmsCaptcha(Request $request)
    {
        return api_response(null, '发送成功);
    }
}