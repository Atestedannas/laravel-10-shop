<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CouponTemplateController extends Controller
{
    /**
     * 鎸?ID 鍒楄〃鑾峰彇浼樻儬鍒告ā鏉?     */
    public function listByIds(Request $request)
    {
        return api_success([]);
    }

    /**
     * 浼樻儬鍒告ā鏉垮垪琛?     */
    public function list(Request $request)
    {
        $mock = [
            [
                'id' => 1,
                'name' => '鏂颁汉涓撲韩鍒?,
                'type' => 1,
                'discount_price' => 10,
                'min_use_price' => 50,
                'valid_start_time' => date('Y-m-d'),
                'valid_end_time' => date('Y-m-d', strtotime('+30 days')),
                'total' => 1000,
                'get_count' => 50,
            ],
            [
                'id' => 2,
                'name' => '婊″噺浼樻儬鍒?,
                'type' => 1,
                'discount_price' => 20,
                'min_use_price' => 100,
                'valid_start_time' => date('Y-m-d'),
                'valid_end_time' => date('Y-m-d', strtotime('+15 days')),
                'total' => 500,
                'get_count' => 120,
            ],
            [
                'id' => 3,
                'name' => '9鎶樺埜',
                'type' => 2,
                'discount_percent' => 90,
                'min_use_price' => 0,
                'valid_start_time' => date('Y-m-d'),
                'valid_end_time' => date('Y-m-d', strtotime('+7 days')),
                'total' => 300,
                'get_count' => 80,
            ],
        ];

        return api_success($mock);
    }

    /**
     * 浼樻儬鍒告ā鏉垮垎椤?     */
    public function page(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }

    /**
     * 浼樻儬鍒告ā鏉胯鎯?     */
    public function get(Request $request)
    {
        $id = $request->input('id');

        $templates = [
            1 => ['id' => 1, 'name' => '鏂颁汉涓撲韩鍒?, 'discount_price' => 10, 'min_use_price' => 50],
            2 => ['id' => 2, 'name' => '婊″噺浼樻儬鍒?, 'discount_price' => 20, 'min_use_price' => 100],
            3 => ['id' => 3, 'name' => '9鎶樺埜', 'discount_percent' => 90, 'min_use_price' => 0],
        ];

        if (!isset($templates[$id])) {
            return api_error(404, '浼樻儬鍒告ā鏉夸笉瀛樺湪');
        }

        return api_success($templates[$id]);
    }
}