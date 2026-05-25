<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\BrokerageUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrokerageUserController extends Controller
{
    /**
     * 绑定分销员（用户申请成为分销员）
     */
    public function bind(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        // 检查是否已是分销员
        $brokerageUser = BrokerageUser::where('user_id', $user->id)->first();
        if ($brokerageUser) {
            return api_error(400, '您已是分销员');
        }

        // 检查是否满足成为分销员的条件（如购买过商品等）
        // 这里可以根据业务需求添加条件检查

        // 创建分销员记录
        $brokerageUser = BrokerageUser::create([
            'user_id' => $user->id,
            'parent_id' => $request->input('parent_id', 0), // 推荐人ID
            'level' => 1, // 默认一级分销员
            'brokerage_price' => 0,
            'frozen_price' => 0,
            'total_brokerage_price' => 0,
            'total_withdraw_price' => 0,
            'user_count' => 0,
            'order_count' => 0,
            'status' => 10, // 待审核
            'apply_time' => now(),
        ]);

        return api_success($brokerageUser, '申请成功，请等待审核');
    }

    /**
     * 获取分销员信息
     */
    public function get(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            $user = auth('sanctum')->user();
            if (!$user) {
                return api_error(401, '请先登录');
            }
            $userId = $user->id;
        }

        $brokerageUser = BrokerageUser::with(['user', 'parent.user'])
            ->where('user_id', $userId)
            ->first();

        if (!$brokerageUser) {
            return api_error(404, '该用户不是分销员');
        }

        // 计算今日收益
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $todayIncome = DB::table('brokerage_records')
            ->where('user_id', $userId)
            ->where('status', 20) // 已结算
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('price');

        // 计算本月收益
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthIncome = DB::table('brokerage_records')
            ->where('user_id', $userId)
            ->where('status', 20)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('price');

        $brokerageUser->today_income = (float) $todayIncome;
        $brokerageUser->month_income = (float) $monthIncome;

        return api_success($brokerageUser);
    }

    /**
     * 获取分销员统计汇总
     */
    public function getSummary(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            $user = auth('sanctum')->user();
            if (!$user) {
                return api_error(401, '请先登录');
            }
            $userId = $user->id;
        }

        $brokerageUser = BrokerageUser::where('user_id', $userId)->first();
        if (!$brokerageUser) {
            return api_error(404, '该用户不是分销员');
        }

        // 统计下级分销员数量
        $childCount = BrokerageUser::where('parent_id', $brokerageUser->id)
            ->where('status', 20) // 已审核通过
            ->count();

        // 统计待结算佣金
        $pendingIncome = DB::table('brokerage_records')
            ->where('user_id', $userId)
            ->where('status', 10) // 待结算
            ->sum('price');

        // 统计已结算佣金
        $settledIncome = DB::table('brokerage_records')
            ->where('user_id', $userId)
            ->where('status', 20) // 已结算
            ->sum('price');

        // 统计提现中金额
        $withdrawing = DB::table('brokerage_withdraws')
            ->where('user_id', $userId)
            ->where('status', 10) // 审核中
            ->sum('price');

        return api_success([
            'child_count' => $childCount,
            'pending_income' => (float) $pendingIncome,
            'settled_income' => (float) $settledIncome,
            'withdrawing' => (float) $withdrawing,
            'available_balance' => $brokerageUser->brokerage_price,
            'frozen_balance' => $brokerageUser->frozen_price,
            'total_income' => $brokerageUser->total_brokerage_price,
            'total_withdraw' => $brokerageUser->total_withdraw_price,
        ]);
    }

    /**
     * 按佣金金额获取排名
     */
    public function getRankByPrice(Request $request)
    {
        $limit = (int) $request->input('limit', 10);
        $userId = $request->input('user_id');

        $query = BrokerageUser::with(['user'])
            ->where('status', 20) // 已审核通过
            ->orderBy('brokerage_price', 'desc')
            ->orderBy('id', 'asc')
            ->limit($limit);

        $rankList = $query->get();

        // 计算当前用户排名
        $userRank = null;
        if ($userId) {
            $userBrokerage = BrokerageUser::where('user_id', $userId)->first();
            if ($userBrokerage) {
                $rank = BrokerageUser::where('status', 20)
                    ->where('brokerage_price', '>', $userBrokerage->brokerage_price)
                    ->count() + 1;
                $userRank = [
                    'rank' => $rank,
                    'brokerage_price' => $userBrokerage->brokerage_price,
                ];
            }
        }

        return api_success([
            'rank_list' => $rankList,
            'user_rank' => $userRank,
        ]);
    }

    /**
     * 按佣金金额排名分页
     */
    public function rankPageByPrice(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 20);
        $keyword = $request->input('keyword');

        $query = BrokerageUser::with(['user'])
            ->where('status', 20);

        // 关键词搜索（用户昵称）
        if ($keyword) {
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where('nickname', 'like', "%{$keyword}%");
            });
        }

        $total = $query->count();
        $list = $query->orderBy('brokerage_price', 'desc')
            ->orderBy('id', 'asc')
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
     * 按下级人数排名分页
     */
    public function rankPageByUserCount(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 20);
        $keyword = $request->input('keyword');

        $query = BrokerageUser::with(['user'])
            ->where('status', 20);

        // 关键词搜索（用户昵称）
        if ($keyword) {
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where('nickname', 'like', "%{$keyword}%");
            });
        }

        $total = $query->count();
        $list = $query->orderBy('user_count', 'desc')
            ->orderBy('id', 'asc')
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
     * 下级分销员汇总分页
     */
    public function childSummaryPage(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 20);
        $level = $request->input('level', 1); // 默认一级下级

        $brokerageUser = BrokerageUser::where('user_id', $user->id)->first();
        if (!$brokerageUser) {
            return api_error(404, '您不是分销员');
        }

        // 根据层级查询下级
        $query = BrokerageUser::with(['user'])
            ->where('status', 20);

        if ($level == 1) {
            $query->where('parent_id', $brokerageUser->id);
        } else {
            // 多级下级查询（需要递归或预先计算层级关系）
            // 这里简化处理，实际业务可能需要更复杂的查询
            $childIds = BrokerageUser::where('parent_id', $brokerageUser->id)
                ->pluck('id')
                ->toArray();
            $query->whereIn('parent_id', $childIds);
        }

        $total = $query->count();
        $list = $query->orderBy('brokerage_price', 'desc')
            ->orderBy('id', 'asc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($item) {
                // 计算该下级的今日收益
                $todayStart = now()->startOfDay();
                $todayEnd = now()->endOfDay();
                $todayIncome = DB::table('brokerage_records')
                    ->where('user_id', $item->user_id)
                    ->where('status', 20)
                    ->whereBetween('created_at', [$todayStart, $todayEnd])
                    ->sum('price');

                $item->today_income = (float) $todayIncome;
                return $item;
            });

        return api_success([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
            'level' => $level,
        ]);
    }
}