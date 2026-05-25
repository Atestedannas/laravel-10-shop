<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Http\Request;

class MyCouponController extends Controller
{
    /**
     * 我的优惠券列表(分页)     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $page    = (int) $request->input('page', 1);
        $perPage = 10;

        $userCoupons = UserCoupon::with('coupon')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $userCoupons->map(function ($uc) {
            $coupon = $uc->coupon;

            return [
                'user_coupon_id' => $uc->id,
                'coupon_id'      => $coupon ? $coupon->id : null,
                'name'           => $coupon ? $coupon->name : '',
                'coupon_type'    => $coupon ? $coupon->coupon_type : null,
                'reduce_price'   => $coupon ? (float) $coupon->reduce_price : 0,
                'discount'       => $coupon ? $coupon->discount : null,
                'min_price'      => $coupon ? (float) $coupon->min_price : 0,
                'expire_type'    => $coupon ? $coupon->expire_type : null,
                'expire_day'     => $coupon ? $coupon->expire_day : null,
                'start_time'     => $coupon ? $coupon->start_time : null,
                'end_time'       => $coupon ? $coupon->end_time : null,
                'describe'       => $coupon ? $coupon->describe : '',
                'apply_range'    => $coupon ? $coupon->apply_range : null,
                'is_used'        => $uc->is_used,
                'is_expired'     => $uc->is_expired,
                'used_at'        => $uc->used_at ? $uc->used_at->toDateTimeString() : null,
                'expired_at'     => $uc->expired_at ? $uc->expired_at->toDateTimeString() : null,
            ];
        });

        return api_response([
            'list'     => $list,
            'total'    => $userCoupons->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }

    /**
     * 领取优惠券     * 参数: couponId
     */
    public function receive(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $couponId = $request->input('couponId');
        if (!$couponId) {
            return api_response(null, '优惠券ID不能为空', 500);
        }

        $coupon = Coupon::find($couponId);
        if (!$coupon) {
            return api_response(null, '优惠券不存在, 500);
        }

        if ($coupon->status != 1) {
            return api_response(null, '优惠券已下架, 500);
        }

        // 检查优惠券库存
        if ($coupon->total_num != -1 && $coupon->receive_num >= $coupon->total_num) {
            return api_response(null, '优惠券已被领完, 400);
        }

        // 检查用户是否已领取
        $exists = UserCoupon::where('user_id', $user->id)
            ->where('coupon_id', $couponId)
            ->exists();

        if ($exists) {
            return api_response(null, '您已领取过此优惠券', 500);
        }

        // 计算过期时间        $expiredAt = null;
        if ($coupon->expire_type == 10) {
            // 领券后N天过期            $expiredAt = now()->addDays((int) $coupon->expire_day);
        } elseif ($coupon->expire_type == 20) {
            // 固定日期过期
            $expiredAt = $coupon->end_time;
        }

        // 创建用户优惠券记录        UserCoupon::create([
            'user_id'    => $user->id,
            'coupon_id'  => $couponId,
            'is_used'    => 0,
            'is_expired' => 0,
            'expired_at' => $expiredAt,
        ]);

        // 更新coupon 閻?receive_num
        $coupon->increment('receive_num');

        return api_response(null, '领取成功');
    }
}