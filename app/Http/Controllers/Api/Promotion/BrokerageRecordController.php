<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\BrokerageRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrokerageRecordController extends Controller
{
    /**
     * 佣金记录分页
     */
    public function page(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $type = $request->input('type');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = BrokerageRecord::with(['order', 'sourceUser'])
            ->where('user_id', $user->id);

        // 类型筛选：10=订单佣金 20=级差佣金 30=其他
        if ($type !== null) {
            $query->where('type', (int) $type);
        }

        // 状态筛选：10=待结算 20=已结算 30=已失效
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        // 时间范围筛选
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
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
     * 获取商品佣金信息（根据商品ID获取佣金比例和金额）
     */
    public function getProductBrokeragePrice(Request $request)
    {
        $goodsId = $request->input('goods_id');
        if (!$goodsId) {
            return api_error(400, '缺少商品ID');
        }

        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        // 查询该商品的佣金配置
        // 佣金配置可能存储在商品表或独立的佣金规则表中
        // 这里从 brokerag_records 表中获取该商品的历史佣金记录作为参考
        $historyRecords = BrokerageRecord::whereHas('order', function ($q) use ($goodsId) {
            $q->whereHas('goods', function ($q2) use ($goodsId) {
                $q2->where('goods_id', $goodsId);
            });
        })
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // 计算平均佣金
        $avgBrokerage = $historyRecords->count() > 0
            ? $historyRecords->avg('price')
            : 0;
        $avgRate = $historyRecords->count() > 0
            ? $historyRecords->avg('brokerage_rate')
            : 0;

        // 查询商品当前价格
        $goods = \App\Models\Goods::find($goodsId);
        if (!$goods) {
            return api_error(404, '商品不存在');
        }

        // 根据佣金率计算预估佣金
        $estimatedBrokerage = 0;
        if ($avgRate > 0) {
            $estimatedBrokerage = round($goods->price * ($avgRate / 100), 2);
        }

        return api_success([
            'goods_id' => $goodsId,
            'goods_price' => $goods->price,
            'avg_brokerage_rate' => (float) $avgRate,
            'avg_brokerage' => (float) $avgBrokerage,
            'estimated_brokerage' => $estimatedBrokerage,
            'history_records' => $historyRecords,
        ]);
    }
}