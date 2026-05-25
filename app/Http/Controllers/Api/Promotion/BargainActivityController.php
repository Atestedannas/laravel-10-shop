<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\BargainActivity;
use App\Models\BargainRecord;
use Illuminate\Http\Request;

class BargainActivityController extends Controller
{
    /**
     * 砍价活动分页
     */
    public function page(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');
        $keyword = $request->input('keyword');
        $orderBy = $request->input('order_by', 'sort'); // sort / origin_price / help_max

        $query = BargainActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }]);

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        } else {
            // 默认只查进行中的
            $now = now();
            $query->where('status', 10)
                ->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now);
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
            case 'origin_price':
                $query->orderBy('origin_price', 'desc');
                break;
            case 'help_max':
                $query->orderBy('help_max', 'asc');
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
     * 砍价活动详情
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return api_error(400, '缺少活动ID');
        }

        $activity = BargainActivity::with(['goods' => function ($q) {
            $q->with(['images', 'skus', 'specs', 'services']);
        }])->find($id);

        if (!$activity) {
            return api_error(404, '砍价活动不存在');
        }

        // 查询该活动正在进行的砍价记录（前5条，用于展示）
        $activeRecords = BargainRecord::with(['user'])
            ->where('activity_id', $id)
            ->where('status', 10) // 砍价中
            ->orderBy('current_price', 'asc')
            ->limit(5)
            ->get();

        $activity->active_records = $activeRecords;

        return api_success($activity);
    }

    /**
     * 我发起的砍价列表
     */
    public function myBargains(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');

        $query = BargainRecord::with([
            'activity' => function ($q) {
                $q->with(['goods' => function ($q2) {
                    $q2->with(['images']);
                }]);
            },
        ])
            ->where('user_id', $user->id);

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($record) {
                $activity = $record->activity;
                $surplus = $record->current_price - ($activity->min_price ?? 0);
                $progress = $record->origin_price > 0
                    ? round(($record->bargain_total / ($record->origin_price - ($activity->min_price ?? 0))) * 100, 1)
                    : 0;

                return [
                    'id' => $record->id,
                    'activity_id' => $record->activity_id,
                    'goods_name' => $activity->goods->goods_name ?? '',
                    'goods_image' => $activity->goods->images->first()->url ?? '',
                    'origin_price' => $record->origin_price,
                    'current_price' => $record->current_price,
                    'bargain_total' => $record->bargain_total,
                    'min_price' => $activity->min_price ?? 0,
                    'surplus_price' => max($surplus, 0),
                    'help_count' => $record->help_count,
                    'help_max' => $activity->help_max ?? 0,
                    'progress' => min($progress, 100),
                    'status' => $record->status,
                    'expire_time' => $record->expire_time,
                    'success_time' => $record->success_time,
                ];
            });

        return api_success([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ]);
    }
}