<?php

namespace App\Http\Controllers\Api\System;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantController extends Controller
{

    /**
     * 閫氳繃鍩熷悕鑾峰彇绉熸埛淇℃伅
     * uniapp: GET /system/tenant/get-by-website?website=xxx
     */
    public function getByWebsite(Request $request)
    {
        $website = $request->input('website', '');

        // 浠庡煙鍚嶈В鏋愮鎴凤紙鐢熶骇鐜闇€鏌ヨ tenants 琛級
        // 褰撳墠涓哄紑鍙戠幆澧冿紝鐩存帴杩斿洖榛樿绉熸埛淇℃伅
        $tenant = [
            'id'      => 1,
            'name'    => '榛樿绉熸埛',
            'website' => $website ?: 'shop.local',
            'logo'    => '/static/images/logo.png',
            'status'  => 1,
        ];

        return api_success($tenant);
    }
}