<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\RewardActivity;
use Illuminate\Http\Request;

class RewardActivityController extends Controller
{
    /**
     * 获取满减满送活动（根据订单金额匹配适用活动）
     */
    public function get(Request $request)
    {
        $orderPrice = (float) $request->input('order_price', 0);
        $goodsIds = $request->input('goods_ids', '');

        if ($orderPrice <= 0) {
            return api_success([]);
        }

        $now = now();

        // 查询当前有效且满足门槛的活动
        $query = RewardActivity::with(['giftGoods'])
            ->where('status', 10)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->where('threshold_price', '<=', $orderPrice);

        // 根据商品范围筛选
        if ($goodsIds) {
            $goodsIdArr = array_filter(array_map('intval', explode(',', $goodsIds)));
            if (!empty($goodsIdArr)) {
                $query->where(function ($q) use ($goodsIdArr) {
                    // scope_type: 1=全部商品 2=指定商品 3=指定分类
                    $q->where('scope_type', 1) // 全部商品可用
                        ->orWhere(function ($q2) use ($goodsIdArr) {
                            $q2->where('scope_type', 2)
                                ->where(function ($q3) use ($goodsIdArr) {
                                    foreach ($goodsIdArr as $gid) {
                                        $q3->orWhereJsonContains('scope_value', (string) $gid);
                                    }
                                });
                        });
                });
            }
        }

        $activities = $query->orderBy('threshold_price', 'desc')
            ->orderBy('sort', 'asc')
            ->get()
            ->map(function ($activity) use ($orderPrice) {
                $result = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'type' => $activity->type,
                    'threshold_price' => $activity->threshold_price,
                    'start_time' => $activity->start_time,
                    'end_time' => $activity->end_time,
                ];

                // 根据活动类型计算优惠
                switch ($activity->type) {
                    case 10: // 满减
                        $result['discount_price'] = $activity->discount_price;
                        $result['after_price'] = round($orderPrice - $activity->discount_price, 2);
                        break;
                    case 20: // 满折
                        $result['discount_rate'] = $activity->discount_price; // 存的是折扣率 如 8.5 表示 8.5 折
                        $result['after_price'] = round($orderPrice * ($activity->discount_price / 10), 2);
                        break;
                    case 30: // 满赠
                        $result['gift_goods_id'] = $activity->gift_goods_id;
                        $result['gift_goods_name'] = $activity->giftGoods->goods_name ?? '';
                        $result['gift_count'] = $activity->gift_count;
                        break;
                }

                return $result;
            });

        return api_success($activities);
    }
}