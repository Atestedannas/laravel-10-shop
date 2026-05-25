<?php

namespace App\Http\Controllers\Api\Migration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * 鑾峰彇灏忕▼搴忕洿鎾埧闂村垪琛?     */
    public function getRoomList(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }

    /**
     * 鑾峰彇灏忕▼搴忕洿鎾摼鎺?     */
    public function getMpLink(Request $request)
    {
        return api_success([
            'url' => '',
        ]);
    }
}