<?php

namespace App\Http\Controllers\Api\Migration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThirdController extends Controller
{
    /**
     * Apple 鐧诲綍
     */
    public function appleLogin(Request $request)
    {
        $identityToken = $request->input('identity_token');

        // 妯℃嫙
        return api_success([
            'access_token' => 'mock_apple_token',
            'token_type' => 'Bearer',
        ]);
    }
}