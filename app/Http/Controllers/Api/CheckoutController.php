<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\GoodsSpecValue;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderGoods;
use App\Models\UserAddress;
use App\Models\UserCoupon;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function order(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $mode        = $request->input('mode', 'cart');
        $delivery    = (int) $request->input('delivery', 10);
        $couponId    = $request->input('couponId', 0);
        $isUsePoints = (bool) $request->input('isUsePoints', false);
        $modeParam   = $request->input('modeParam', []);

        $goodsList = [];
        $orderTotalNum   = 0;
        $orderTotalPrice = 0;

        if ($mode === 'buyNow') {
            $goodsId    = $modeParam['goodsId'] ?? 0;
            $goodsNum   = (int) ($modeParam['goodsNum'] ?? 1);
            $goodsSkuId = $modeParam['goodsSkuId'] ?? 0;

            $goods = Goods::find($goodsId);
            $sku   = GoodsSku::find($goodsSkuId);

            if ($goods) {
                $skuPrice    = $sku ? (float) $sku->goods_price : (float) $goods->goods_price_min;
                $linePrice   = $sku ? (float) $sku->line_price : (float) $goods->line_price_min;
                $skuSpecIds  = $sku ? $sku->sku_spec_ids : '';

                $goodsProps = [];
                if ($skuSpecIds) {
                    $specValueIds = array_map('intval', explode(',', $skuSpecIds));
                    $specValues = GoodsSpecValue::whereIn('id', $specValueIds)->get();
                    foreach ($specValues as $sv) {
                        $goodsProps[] = ['value' => ['name' => $sv->spec_value]];
                    }
                }

                $orderTotalNum   += $goodsNum;
                $orderTotalPrice += $skuPrice * $goodsNum;

                $goodsList[] = [
                    'goods_id'          => $goods->id,
                    'goods_name'        => $goods->goods_name,
                    'goods_image'       => $goods->goods_image,
                    'goods_price'       => $skuPrice,
                    'total_num'         => $goodsNum,
                    'is_user_grade'     => $goods->is_user_grade,
                    'grade_goods_price' => $skuPrice,
                    'skuInfo'           => ['goods_props' => $goodsProps],
                ];
            }
        } else {
            $cartIds = $modeParam['cartIds'] ?? [];
            if (empty($cartIds)) {
                return api_response(null, '请选择要结算的商品', 400);
            }

            $carts = Cart::where('user_id', $user->id)
                ->whereIn('id', $cartIds)
                ->get();

            $goodsIds = $carts->pluck('goods_id')->unique();
            $skuIds   = $carts->pluck('goods_sku_id')->unique();

            $goodsMap = Goods::whereIn('id', $goodsIds)->get()->keyBy('id');
            $skuMap   = GoodsSku::whereIn('id', $skuIds)->get()->keyBy('id');

            foreach ($carts as $cart) {
                $goods = $goodsMap->get($cart->goods_id);
                $sku   = $skuMap->get($cart->goods_sku_id);

                if (!$goods) continue;

                $skuPrice  = $sku ? (float) $sku->goods_price : (float) $goods->goods_price_min;
                $linePrice = $sku ? (float) $sku->line_price : (float) $goods->line_price_min;

                $goodsProps = [];
                if ($sku && $sku->sku_spec_ids) {
                    $specValueIds = array_map('intval', explode(',', $sku->sku_spec_ids));
                    $specValues = GoodsSpecValue::whereIn('id', $specValueIds)->get();
                    foreach ($specValues as $sv) {
                        $goodsProps[] = ['value' => ['name' => $sv->spec_value]];
                    }
                }

                $orderTotalNum   += (int) $cart->goods_num;
                $orderTotalPrice += $skuPrice * (int) $cart->goods_num;

                $goodsList[] = [
                    'goods_id'          => $goods->id,
                    'goods_name'        => $goods->goods_name,
                    'goods_image'       => $goods->goods_image,
                    'goods_price'       => $skuPrice,
                    'total_num'         => (int) $cart->goods_num,
                    'is_user_grade'     => $goods->is_user_grade,
                    'grade_goods_price' => $skuPrice,
                    'skuInfo'           => ['goods_props' => $goodsProps],
                ];
            }
        }

        $address = UserAddress::where('user_id', $user->id)
            ->where('is_default', 1)
            ->first();

        $addressData = null;
        if ($address) {
            $addressData = [
                'name'   => $address->name,
                'phone'  => $address->phone,
                'region' => [$address->province_id, $address->city_id, $address->region_id],
                'detail' => $address->detail,
            ];
        }

        $userCoupons = UserCoupon::where('user_id', $user->id)
            ->where('is_used', 0)
            ->where('is_expired', 0)
            ->with('coupon')
            ->get();

        $couponList = $userCoupons->map(function ($uc) {
            $c = $uc->coupon;
            if (!$c) return null;
            return [
                'user_coupon_id' => $uc->id,
                'name'           => $c->name,
                'coupon_type'    => $c->coupon_type,
                'reduce_price'   => (float) $c->reduce_price,
                'discount'       => $c->discount,
                'min_price'      => (float) $c->min_price,
                'describe'       => $c->describe,
                'apply_range'    => $c->apply_range,
            ];
        })->filter()->values();

        $couponMoney = 0;
        if ($couponId) {
            $userCoupon = UserCoupon::where('user_id', $user->id)
                ->where('id', $couponId)
                ->first();
            if ($userCoupon && $userCoupon->coupon) {
                $c = $userCoupon->coupon;
                if ($c->coupon_type == 10) {
                    $couponMoney = (float) $c->reduce_price;
                } elseif ($c->coupon_type == 20) {
                    $couponMoney = round($orderTotalPrice * ((100 - $c->discount) / 100), 2);
                }
            }
        }

        $pointsMoney = 0;
        $profile = UserProfile::where('user_id', $user->id)->first();
        $userPoints = (int) ($profile->points ?? 0);
        $isAllowPoints = $userPoints > 0;

        if ($isUsePoints && $isAllowPoints) {
            $pointsMoney = min($userPoints / 100, $orderTotalPrice - $couponMoney);
            $pointsMoney = round($pointsMoney, 2);
        }

        $expressPrice = '0.00';

        $orderPayPrice = max(0, $orderTotalPrice - $couponMoney - $pointsMoney + (float) $expressPrice);
        $orderPayPrice = round($orderPayPrice, 2);

        $orderData = [
            'orderType'       => 10,
            'delivery'        => $delivery,
            'address'         => $addressData,
            'goodsList'       => $goodsList,
            'orderTotalNum'   => $orderTotalNum,
            'orderTotalPrice' => sprintf('%.2f', $orderTotalPrice),
            'orderPayPrice'   => sprintf('%.2f', $orderPayPrice),
            'couponList'      => $couponList,
            'couponId'        => (int) $couponId,
            'couponMoney'     => $couponMoney,
            'isAllowPoints'   => $isAllowPoints,
            'isUsePoints'     => $isUsePoints,
            'pointsMoney'     => $pointsMoney,
            'expressPrice'    => $expressPrice,
            'isIntraRegion'   => true,
            'hasError'        => false,
            'errorMsg'        => '',
        ];

        $personal = [
            'userId'   => $user->id,
            'nickname' => $user->nickname,
            'avatar'   => $user->avatar,
            'balance'  => (float) ($profile->balance ?? 0),
            'points'   => $userPoints,
        ];

        return api_response([
            'order'    => $orderData,
            'setting'  => [
                'deliveryType'    => [10, 30],
                'points_name'     => '积分',
                'points_describe' => '100积分抵1元',
            ],
            'personal' => $personal,
        ]);
    }

    public function submit(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $mode        = $request->input('mode', 'cart');
        $delivery    = (int) $request->input('delivery', 10);
        $couponId    = $request->input('couponId', 0);
        $isUsePoints = (bool) $request->input('isUsePoints', false);
        $remark      = $request->input('remark', '');
        $modeParam   = $request->input('modeParam', []);

        $goodsItems = [];
        if ($mode === 'buyNow') {
            $goodsId    = $modeParam['goodsId'] ?? 0;
            $goodsNum   = (int) ($modeParam['goodsNum'] ?? 1);
            $goodsSkuId = $modeParam['goodsSkuId'] ?? 0;
            $goodsItems[] = ['goodsId' => $goodsId, 'goodsSkuId' => $goodsSkuId, 'goodsNum' => $goodsNum];
        } else {
            $cartIds = $modeParam['cartIds'] ?? [];
            $carts = Cart::where('user_id', $user->id)->whereIn('id', $cartIds)->get();
            foreach ($carts as $cart) {
                $goodsItems[] = [
                    'goodsId'    => $cart->goods_id,
                    'goodsSkuId' => $cart->goods_sku_id,
                    'goodsNum'   => $cart->goods_num,
                ];
            }
        }

        if (empty($goodsItems)) {
            return api_response(null, '商品信息为空', 500);
        }

        $totalPrice  = 0;
        $couponMoney = 0;
        $pointsMoney = 0;

        $goodsIds = array_column($goodsItems, 'goodsId');
        $skuIds   = array_column($goodsItems, 'goodsSkuId');

        $goodsMap = Goods::whereIn('id', $goodsIds)->get()->keyBy('id');
        $skuMap   = GoodsSku::whereIn('id', $skuIds)->get()->keyBy('id');

        foreach ($goodsItems as $item) {
            $sku = $skuMap->get($item['goodsSkuId']);
            $price = $sku ? (float) $sku->goods_price : (float) ($goodsMap->get($item['goodsId'])->goods_price_min ?? 0);
            $totalPrice += $price * $item['goodsNum'];
        }

        if ($couponId) {
            $userCoupon = UserCoupon::where('user_id', $user->id)->where('id', $couponId)->first();
            if ($userCoupon && $userCoupon->coupon) {
                $c = $userCoupon->coupon;
                if ($c->coupon_type == 10) {
                    $couponMoney = (float) $c->reduce_price;
                } elseif ($c->coupon_type == 20) {
                    $couponMoney = round($totalPrice * ((100 - $c->discount) / 100), 2);
                }
                $userCoupon->is_used = 1;
                $userCoupon->used_at = now();
                $userCoupon->save();
            }
        }

        if ($isUsePoints) {
            $profile = UserProfile::where('user_id', $user->id)->first();
            $userPoints = (int) ($profile->points ?? 0);
            $pointsMoney = min($userPoints / 100, $totalPrice - $couponMoney);
            $pointsMoney = round($pointsMoney, 2);
        }

        $payPrice = max(0, $totalPrice - $couponMoney - $pointsMoney);
        $orderNo  = date('YmdHis') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'user_id'         => $user->id,
            'order_no'        => $orderNo,
            'order_status'    => 10,
            'pay_status'      => 10,
            'delivery_status' => 10,
            'receipt_status'  => 10,
            'order_type'      => 10,
            'delivery_type'   => $delivery,
            'total_price'     => $totalPrice,
            'coupon_money'    => $couponMoney,
            'points_money'    => $pointsMoney,
            'express_price'   => 0,
            'pay_price'       => $payPrice,
            'buyer_remark'    => $remark,
            'coupon_id'       => $couponId,
            'is_use_points'   => $isUsePoints ? 1 : 0,
            'create_time'     => now()->toDateTimeString(),
        ]);

        foreach ($goodsItems as $item) {
            $goods = $goodsMap->get($item['goodsId']);
            $sku   = $skuMap->get($item['goodsSkuId']);

            $skuSpecIds = $sku ? $sku->sku_spec_ids : '';
            $goodsPropsJson = '[]';
            if ($skuSpecIds) {
                $specValueIds = array_map('intval', explode(',', $skuSpecIds));
                $specValues = GoodsSpecValue::whereIn('id', $specValueIds)->get();
                $props = [];
                foreach ($specValues as $sv) {
                    $props[] = ['value' => ['name' => $sv->spec_value]];
                }
                $goodsPropsJson = json_encode($props, JSON_UNESCAPED_UNICODE);
            }

            OrderGoods::create([
                'order_id'      => $order->id,
                'goods_id'      => $item['goodsId'],
                'goods_sku_id'  => $item['goodsSkuId'],
                'goods_name'    => $goods->goods_name ?? '',
                'goods_image'   => $goods->goods_image ?? '',
                'goods_price'   => $sku ? (float) $sku->goods_price : (float) ($goods->goods_price_min ?? 0),
                'total_num'     => $item['goodsNum'],
                'goods_props'   => $goodsPropsJson,
                'is_user_grade' => $goods->is_user_grade ?? 0,
            ]);
        }

        $defaultAddress = UserAddress::where('user_id', $user->id)
            ->where('is_default', 1)
            ->first();

        if ($defaultAddress) {
            OrderAddress::create([
                'order_id' => $order->id,
                'name'     => $defaultAddress->name,
                'phone'    => $defaultAddress->phone,
                'province' => (string) $defaultAddress->province_id,
                'city'     => (string) $defaultAddress->city_id,
                'region'   => (string) $defaultAddress->region_id,
                'detail'   => $defaultAddress->detail,
            ]);
        }

        if ($mode === 'cart') {
            $cartIds = $modeParam['cartIds'] ?? [];
            Cart::where('user_id', $user->id)->whereIn('id', $cartIds)->delete();
        }

        return api_response([
            'orderId'      => $order->id,
            'isPaySuccess' => false,
        ]);
    }
}
