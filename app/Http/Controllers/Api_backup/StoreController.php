<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;

class StoreController extends Controller
{
    /**
     * 鍟嗗煄鍩虹淇℃伅
     * 杩斿洖: { storeInfo, setting, clientData }
     */
    public function data()
    {
        $storeInfo = [
            'name'        => 'Laravel Shop',
            'logo'        => '',
            'description' => '',
        ];

        // 澶嶇敤 SettingController 鐨勮缃鍙栭€昏緫
        $settings = StoreSetting::all();
        $setting = [
            'register'               => [],
            'app_theme'              => [],
            'page_category_template' => [],
            'points'                 => [],
            'recharge'               => [],
            'customer'               => [],
        ];

        foreach ($settings as $item) {
            $value = $item->value;
            $decoded = json_decode($value, true);
            $setting[$item->key] = $decoded !== null ? $decoded : $value;
        }

        return api_response([
            'storeInfo'  => $storeInfo,
            'setting'    => $setting,
            'clientData' => [],
        ]);
    }
}