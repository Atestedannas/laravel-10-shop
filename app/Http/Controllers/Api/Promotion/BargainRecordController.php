<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\BargainRecord;
use App\Models\BargainActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BargainRecordController extends Controller
{
    /**
     * 砍价记录分页（所有砍价记录）
     */
    public function page(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');
        $activityId = $request->input('activity_id');
        $keyword = $request->input('keyword');

        $query = BargainRecord::with([
            'activity' => function ($q) {
                $q->with(['goods' => function ($q2) {
                    $q2->with(['images']);
                }]);
            },
            'user',
        ]);

        // 状态筛选
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        // 活动筛选
        if ($activityId) {
            $query->where('activity_id', (int) $activityId);
        }

        // 关键词搜索（通过商品名称或用户昵称）
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('activity.goods', function ($q2) use ($keyword) {
                    $q2->where('goods_name', 'like', "%{$keyword}%");
                })->orWhereHas('user', function ($q2) use ($keyword) {
                    $q2->where('nickname', 'like', "%{$keyword}%");
                });
            });
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
     * 帮砍接口
     */
    public function help(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $recordId = $request->input('record_id');
        if (!$recordId) {
            return api_error(400, '缺少砍价记录ID');
        }

        $record = BargainRecord::with(['activity'])->find($recordId);
        if (!$record) {
            return api_error(404, '砍价记录不存在');
        }

        // 检查砍价状态
        if ($record->status != 10) {
            return api_error(400, '该砍价已结束');
        }

        // 检查是否已过期
        if (now()->gt($record->expire_time)) {
            $record->status = 30; // 砍价失败
            $record->save();
            return api_error(400, '该砍价已过期');
        }

        // 检查是否已砍到最低价
        $activity = $record->activity;
        if ($record->current_price <= $activity->min_price) {
            $record->status = 20; // 砍价成功
            $record->success_time = now();
            $record->save();
            return api_error(400, '该砍价已成功');
        }

        // 检查是否已达到最大帮砍次数
        if ($record->help_count >= $activity->help_max) {
            return api_error(400, '该砍价已达到最大帮砍次数');
        }

        // 检查用户是否已帮砍过（同一用户只能帮砍一次）
        // 这里需要一个独立的帮砍记录表，暂时简化处理
        // $hasHelped = BargainHelp::where('record_id', $recordId)->where('user_id', $user->id)->exists();
        // if ($hasHelped) {
        //     return api_error(400, '您已帮砍过，不能重复帮砍');
        // }

        // 计算砍价金额（根据砍价模式）
        $bargainAmount = $this->calculateBargainAmount($activity, $record);

        // 更新砍价记录
        $record->current_price = max($record->current_price - $bargainAmount, $activity->min_price);
        $record->bargain_total += $bargainAmount;
        $record->help_count += 1;

        // 检查是否砍到最低价
        if ($record->current_price <= $activity->min_price) {
            $record->status = 20; // 砍价成功
            $record->success_time = now();
        }

        $record->save();

        // 记录帮砍记录（需要独立的帮砍表）
        // BargainHelp::create([
        //     'record_id' => $recordId,
        //     'user_id' => $user->id,
        //     'bargain_amount' => $bargainAmount,
        // ]);

        return api_success([
            'bargain_amount' => $bargainAmount,
            'current_price' => $record->current_price,
            'bargain_total' => $record->bargain_total,
            'help_count' => $record->help_count,
            'status' => $record->status,
            'surplus_price' => max($record->current_price - $activity->min_price, 0),
        ], '帮砍成功');
    }

    /**
     * 砍价进度查询
     */
    public function getProgress(Request $request)
    {
        $recordId = $request->input('id');
        if (!$recordId) {
            return api_error(400, '缺少砍价记录ID');
        }

        $record = BargainRecord::with([
            'activity' => function ($q) {
                $q->with(['goods' => function ($q2) {
                    $q2->with(['images']);
                }]);
            },
            'user',
        ])->find($recordId);

        if (!$record) {
            return api_error(404, '砍价记录不存在');
        }

        $activity = $record->activity;
        $surplus = max($record->current_price - $activity->min_price, 0);
        $progress = $record->origin_price > 0
            ? round(($record->bargain_total / ($record->origin_price - $activity->min_price)) * 100, 1)
            : 0;

        // 查询帮砍记录（需要独立的帮砍表）
        // $helpRecords = BargainHelp::with(['user'])->where('record_id', $recordId)->get();

        $result = [
            'id' => $record->id,
            'activity_id' => $record->activity_id,
            'goods_name' => $activity->goods->goods_name ?? '',
            'goods_image' => $activity->goods->images->first()->url ?? '',
            'origin_price' => $record->origin_price,
            'current_price' => $record->current_price,
            'min_price' => $activity->min_price,
            'surplus_price' => $surplus,
            'bargain_total' => $record->bargain_total,
            'help_count' => $record->help_count,
            'help_max' => $activity->help_max,
            'progress' => min($progress, 100),
            'status' => $record->status,
            'expire_time' => $record->expire_time,
            'success_time' => $record->success_time,
            'user' => $record->user,
            // 'help_records' => $helpRecords,
        ];

        return api_success($result);
    }

    /**
     * 计算砍价金额
     */
    private function calculateBargainAmount($activity, $record)
    {
        $currentPrice = $record->current_price;
        $minPrice = $activity->min_price;
        $surplus = $currentPrice - $minPrice;

        // 根据砍价模式计算
        switch ($activity->bargain_mode) {
            case 10: // 随机金额模式
                $minAmount = $activity->bargain_min;
                $maxAmount = min($activity->bargain_max, $surplus);
                if ($maxAmount <= $minAmount) {
                    return round($surplus, 2);
                }
                return round(mt_rand($minAmount * 100, $maxAmount * 100) / 100, 2);

            case 20: // 固定金额模式
                $fixedAmount = $activity->bargain_min;
                return min($fixedAmount, $surplus);

            case 30: // 递增金额模式
                $baseAmount = $activity->bargain_min;
                $increasePerHelp = $activity->bargain_max;
                $increaseAmount = $baseAmount + ($record->help_count * $increasePerHelp);
                return min($increaseAmount, $surplus);

            default:
                return round(min($surplus * 0.05, 10), 2); // 默认砍5%或最多10元
        }
    }
}