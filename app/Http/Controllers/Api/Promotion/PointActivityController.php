<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\PointActivity;
use Illuminate\Http\Request;

class PointActivityController extends Controller
{
    /**
     * 积分商城分页
     */
    public function page(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');
        $keyword = $request->input('keyword');
        $orderBy = $request->input('order_by', 'sort'); // sort / point_price / sold_count

        $query = PointActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }]);

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        } else {
            // 默认只查进行中的
            $query->where('status', 10)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now());
        }

        // 关键词搜索（通过商品名称）
        if ($keyword) {
            $query->whereHas('goods', function ($q) use ($keyword) {
                $q->where('goods_name', 'like', "%{$keyword}%");
            });
        }

        $total = $query->count();

        // 排序
        switch ($orderBy) {
            case 'point_price':
                $query->orderBy('point_price', 'asc');
                break;
            case 'sold_count':
                $query->orderBy('sold_count', 'desc');
                break;
            default:
                $query->orderBy('sort', 'asc');
                break;
        }
        $query->orderBy('id', 'desc');

        $list = $query->skip(($page - 1) * $pageSize)
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
     * 按 ID 列表获取积分商品
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

        $activities = PointActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }])
            ->whereIn('id', $idArr)
            ->get();

        return api_success($activities);
    }

    /**
     * 积分商品详情
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return api_error(400, '缺少活动ID');
        }

        $activity = PointActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus', 'specs', 'services']);
        }])->find($id);

        if (!$activity) {
            return api_error(404, '积分商品不存在');
        }

        return api_success($activity);
    }
}