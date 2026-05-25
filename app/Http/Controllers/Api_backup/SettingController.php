<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;

class SettingController extends Controller
{
    /**
     * 鍟嗗煄璁剧疆
     * 杩斿洖: { setting: { register:{...}, app_theme:{...}, ... } }
     */
    public function data()
    {
        $settings = StoreSetting::all();

        $setting = [
            'register'              => [],
            'app_theme'             => [],
            'page_category_template' => [],
            'points'                => [],
            'recharge'              => [],
            'customer'              => [],
        ];

        foreach ($settings as $item) {
            $value = $item->value;
            $decoded = json_decode($value, true);
            $setting[$item->key] = $decoded !== null ? $decoded : $value;
        }

        return api_response([
            'setting' => $setting,
        ]);
    }
}