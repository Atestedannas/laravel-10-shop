<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * 可领取优惠券列表     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();

        // 查询全部有效优惠券
        $coupons = Coupon::where('status', 1)->get();

        // 获取用户已领取优惠券ID集合
        $userCouponIds = [];
        if ($user) {
            $userCouponIds = UserCoupon::where('user_id', $user->id)
                ->pluck('coupon_id')
                ->toArray();
        }

        $list = $coupons->filter(function ($coupon) {
            // 排除已领完的
            if ($coupon->total_num != -1 && $coupon->receive_num >= $coupon->total_num) {
                return false;
            }
            return true;
        })->map(function ($coupon) use ($userCouponIds) {
            return [
                'coupon_id'    => $coupon->id,
                'name'         => $coupon->name,
                'coupon_type'  => $coupon->coupon_type,
                'reduce_price' => (float) $coupon->reduce_price,
                'discount'     => $coupon->discount,
                'min_price'    => (float) $coupon->min_price,
                'expire_type'  => $coupon->expire_type,
                'expire_day'   => $coupon->expire_day,
                'start_time'   => $coupon->start_time,
                'end_time'     => $coupon->end_time,
                'describe'     => $coupon->describe,
                'apply_range'  => $coupon->apply_range,
                'is_receive'   => in_array($coupon->id, $userCouponIds),
            ];
        })->values();

        return api_response($list);
    }
}