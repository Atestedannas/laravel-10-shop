<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\CombinationActivity;
use Illuminate\Http\Request;

class CombinationActivityController extends Controller
{
    /**
     * 拼团活动分页
     */
    public function page(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');
        $keyword = $request->input('keyword');

        $query = CombinationActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }]);

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        // 关键词搜索（通过商品名称）
        if ($keyword) {
            $query->whereHas('goods', function ($q) use ($keyword) {
                $q->where('goods_name', 'like', "%{$keyword}%");
            });
        }

        // 只查询未删除的
        $query->whereNull('deleted_at');

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
     * 拼团活动详情
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return api_error(400, '缺少活动ID');
        }

        $activity = CombinationActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus', 'specs', 'services']);
        }, 'records' => function ($q) {
            $q->with(['user'])->where('status', 10)->limit(10);
        }])->find($id);

        if (!$activity) {
            return api_error(404, '拼团活动不存在');
        }

        return api_success($activity);
    }

    /**
     * 按 ID 列表获取拼团活动
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

        $activities = CombinationActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }])
            ->whereIn('id', $idArr)
            ->whereNull('deleted_at')
            ->get();

        return api_success($activities);
    }
}