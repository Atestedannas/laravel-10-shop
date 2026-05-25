<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class DiyPageController extends Controller
{
    /**
     * 鑾峰彇椤甸潰璇︽儏
     */
    public function get(Request $request)
    {
        $id = $request->input('id');

        $page = Page::find($id);
        if (!$page) {
            return api_error(404, '椤甸潰涓嶅瓨鍦?);
        }

        return api_success($page);
    }
}