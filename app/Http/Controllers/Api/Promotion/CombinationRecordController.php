<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\CombinationRecord;
use App\Models\CombinationActivity;
use Illuminate\Http\Request;

class CombinationRecordController extends Controller
{
    /**
     * 拼团头部列表（按活动 ID 获取正在拼团的记录，用于商品详情页展示）
     */
    public function getHeadList(Request $request)
    {
        $activityId = $request->input('activity_id');
        if (!$activityId) {
            return api_error(400, '缺少活动ID');
        }

        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 5);

        $query = CombinationRecord::with(['user', 'activity'])
            ->where('activity_id', (int) $activityId)
            ->where('status', 10); // 只查询拼团中的

        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'group_no' => $record->group_no,
                    'user_id' => $record->user_id,
                    'nickname' => $record->user->profile->nickname ?? '',
                    'avatar' => $record->user->profile->avatar ?? '',
                    'required_count' => $record->required_count,
                    'current_count' => $record->current_count,
                    'surplus_count' => $record->required_count - $record->current_count,
                    'expire_time' => $record->expire_time,
                ];
            });

        return api_success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * 拼团记录分页（用户参与的拼团记录）
     */
    public function page(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');

        $query = CombinationRecord::with([
            'activity' => function ($q) {
                $q->with(['goods' => function ($q2) {
                    $q2->with(['images']);
                }]);
            },
            'user',
        ])->whereHas('activity', function ($q) {
            $q->whereNull('deleted_at');
        });

        // 当前用户作为团长的记录
        $query->where('user_id', $user->id);

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
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
     * 拼团记录详情
     */
    public function getDetail(Request $request)
    {
        $groupId = $request->input('id');
        if (!$groupId) {
            return api_error(400, '缺少拼团记录ID');
        }

        $record = CombinationRecord::with([
            'activity' => function ($q) {
                $q->with(['goods' => function ($q2) {
                    $q2->with(['images', 'skus']);
                }]);
            },
            'user',
        ])->find($groupId);

        if (!$record) {
            return api_error(404, '拼团记录不存在');
        }

        return api_success($record);
    }

    /**
     * 拼团记录汇总（统计拼团数量）
     */
    public function getSummary(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $userId = $user->id;

        // 拼团中的数量
        $inProgressCount = CombinationRecord::where('user_id', $userId)
            ->where('status', 10)
            ->count();

        // 拼团成功的数量
        $successCount = CombinationRecord::where('user_id', $userId)
            ->where('status', 20)
            ->count();

        // 拼团失败的数量
        $failCount = CombinationRecord::where('user_id', $userId)
            ->where('status', 30)
            ->count();

        return api_success([
            'in_progress_count' => $inProgressCount,
            'success_count' => $successCount,
            'fail_count' => $failCount,
        ]);
    }
}