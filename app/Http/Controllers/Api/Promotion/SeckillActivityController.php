<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\SeckillActivity;
use App\Models\SeckillConfig;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SeckillActivityController extends Controller
{
    /**
     * 当前可参与的秒杀活动
     */
    public function getNow()
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $currentDate = $now->format('Y-m-d');

        // 查找当前时段已启用的秒杀配置
        $config = SeckillConfig::where('status', 10)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->orderBy('sort', 'asc')
            ->first();

        if (!$config) {
            return api_success([
                'config' => null,
                'list' => [],
            ]);
        }

        // 查询当前时段对应日期的秒杀活动
        $activities = SeckillActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }])
            ->where('config_id', $config->id)
            ->where('start_date', $currentDate)
            ->where('status', 10)
            ->whereNull('deleted_at')
            ->orderBy('sort', 'asc')
            ->get();

        return api_success([
            'config' => [
                'id' => $config->id,
                'name' => $config->name,
                'start_time' => $config->start_time,
                'end_time' => $config->end_time,
            ],
            'list' => $activities,
        ]);
    }

    /**
     * 秒杀活动分页
     */
    public function page(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');
        $configId = $request->input('config_id');
        $date = $request->input('date');
        $keyword = $request->input('keyword');

        $query = SeckillActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }, 'config'])
            ->whereNull('deleted_at');

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        // 时段筛选
        if ($configId) {
            $query->where('config_id', (int) $configId);
        }

        // 日期筛选
        if ($date) {
            $query->where('start_date', $date);
        }

        // 关键词搜索（通过商品名称）
        if ($keyword) {
            $query->whereHas('goods', function ($q) use ($keyword) {
                $q->where('goods_name', 'like', "%{$keyword}%");
            });
        }

        $total = $query->count();
        $list = $query->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return api_success([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * 按 ID 列表获取秒杀活动
     */
    public function listByIds(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids) {
            return api_success([]);
        }

        $idArr = array_filter(
            array_map('intval', explode(',', $ids))
        );

        if (empty($idArr)) {
            return api_success([]);
        }

        $activities = SeckillActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }])
            ->whereIn('id', $idArr)
            ->whereNull('deleted_at')
            ->get();

        return api_success($activities);
    }

    /**
     * 秒杀活动详情
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return api_error(400, '缺少活动ID');
        }

        $activity = SeckillActivity::with([
            'goods' => function ($q) {
                $q->with(['images', 'skus', 'specs', 'services']);
            },
            'config',
        ])->find($id);

        if (!$activity) {
            return api_error(404, '秒杀活动不存在');
        }

        return api_success($activity);
    }
}