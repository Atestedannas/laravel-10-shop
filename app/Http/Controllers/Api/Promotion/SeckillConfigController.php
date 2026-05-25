<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\SeckillConfig;
use Illuminate\Http\Request;

class SeckillConfigController extends Controller
{
    /**
     * 秒杀配置列表
     */
    public function list(Request $request)
    {
        $status = $request->input('status');
        $keyword = $request->input('keyword');

        $query = SeckillConfig::query();

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        // 关键词搜索
        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        $list = $query->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return api_success($list);
    }
}