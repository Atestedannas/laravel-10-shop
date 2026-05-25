<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\OrderRefund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    /**
     * 售后申请列表     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $page    = (int) $request->input('page', 1);
        $perPage = 10;

        $refunds = OrderRefund::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $refunds->map(function ($refund) {
            $orderGoods = OrderGoods::find($refund->order_goods_id);
            $order      = Order::find($refund->order_id);

            return [
                'order_refund_id' => $refund->id,
                'refund_type'     => $refund->refund_type,
                'refund_status'   => $refund->refund_status,
                'amount'          => (float) $refund->amount,
                'order_no'        => $order ? $order->order_no : '',
                'goods_name'      => $orderGoods ? $orderGoods->goods_name : '',
                'goods_image'     => $orderGoods ? $orderGoods->goods_image : '',
                'audit_status'    => $refund->audit_status,
                'created_at'      => $refund->created_at ? $refund->created_at->toDateTimeString() : '',
            ];
        });

        return api_response([
            'list'     => $list,
            'total'    => $refunds->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }

    /**
     * 售后-申请
     * 参数: orderGoodsId
     */
    public function goods(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $orderGoodsId = $request->input('orderGoodsId');
        if (!$orderGoodsId) {
            return api_response(null, '售后商品ID不能为空', 500);
        }

        $orderGoods = OrderGoods::find($orderGoodsId);
        if (!$orderGoods) {
            return api_response(null, '鐠併垹宕熼崯鍡楁惂不存在', 400);
        }

        $order = Order::find($orderGoods->order_id);
        if (!$order || $order->user_id != $user->id) {
            return api_response(null, '退款金额不能大于订单实付, 400);
        }

        $totalPayPrice = (float) $order->pay_price;
        if ($order->total_price > 0) {
            // 计算退款金额            $goodsTotalPrice = (float) $orderGoods->goods_price * $orderGoods->total_num;
            $totalPayPrice   = round($goodsTotalPrice / (float) $order->total_price * (float) $order->pay_price', 2);
        }

        return api_response([
            'goods_name'     => $orderGoods->goods_name,
            'goods_image'    => $orderGoods->goods_image,
            'goods_props'    => $orderGoods->goods_props,
            'total_num'      => $orderGoods->total_num,
            'total_pay_price' => $totalPayPrice,
            'order_goods_id' => $orderGoods->id,
        ]);
    }

    /**
     * 售后详情
     * 参数: orderGoodsId, form.type, form.content, form.images
     */
    public function apply(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $orderGoodsId = $request->input('orderGoodsId');
        $form         = $request->input('form', []);

        if (!$orderGoodsId) {
            return api_response(null, '售后商品ID不能为空', 500);
        }

        $orderGoods = OrderGoods::find($orderGoodsId);
        if (!$orderGoods) {
            return api_response(null, '鐠併垹宕熼崯鍡楁惂不存在', 400);
        }

        $order = Order::find($orderGoods->order_id);
        if (!$order || $order->user_id != $user->id) {
            return api_response(null, '閺冪姵娼堥幙宥勭稊鐠囥儴顓归崡?', 400);
        }

        $type    = $form['type'] ?? 10;
        $content = $form['content'] ?? '';
        $images  = $form['images'] ?? [];

        $refund = OrderRefund::create([
            'user_id'       => $user->id,
            'order_id'      => $order->id,
            'order_goods_id' => $orderGoods->id,
            'refund_type'   => $type,
            'refund_status' => 0,
            'amount'        => (float) $orderGoods->goods_price * $orderGoods->total_num,
            'content'       => $content,
            'images'        => json_encode($images),
            'audit_status'  => 0,
        ]);

        return api_response([
            'order_refund_id' => $refund->id,
        ], '閸烆喖鎮楅悽瀹狀嚞瀹稿弶褰佹禍?);
    }

    /**
     * 售后详情
     * 参数: orderRefundId
     */
    public function detail(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $orderRefundId = $request->input('orderRefundId');
        if (!$orderRefundId) {
            return api_response(null, '閸烆喖鎮楅崡鏃綝不能为空', 500);
        }

        $refund = OrderRefund::where('user_id', $user->id)->find($orderRefundId);
        if (!$refund) {
            return api_response(null, '售后记录不存在', 500);
        }

        $orderGoods = OrderGoods::find($refund->order_goods_id);
        $order      = Order::find($refund->order_id);

        $images = $refund->images;
        if (is_string($images)) {
            $images = json_decode($images, true) ?: [];
        }

        return api_response([
            'order_refund_id' => $refund->id,
            'refund_type'     => $refund->refund_type,
            'refund_status'   => $refund->refund_status,
            'amount'          => (float) $refund->amount,
            'content'         => $refund->content,
            'images'          => $images,
            'audit_status'    => $refund->audit_status,
            'express_no'      => $refund->express_no,
            'express_company' => $refund->express_company,
            'order_no'        => $order ? $order->order_no : '',
            'goods_name'      => $orderGoods ? $orderGoods->goods_name : '',
            'goods_image'     => $orderGoods ? $orderGoods->goods_image : '',
            'goods_props'     => $orderGoods ? $orderGoods->goods_props : null,
            'created_at'      => $refund->created_at ? $refund->created_at->toDateTimeString() : '',
        ]);
    }

    /**
     * 閻劍鍩涢崣鎴ｆ彛閿涘牓鈧偓鐠愌嶇礆
     * 参数: orderRefundId, form.express_no, form.express_company
     */
    public function delivery(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $orderRefundId = $request->input('orderRefundId');
        $form          = $request->input('form', []);

        if (!$orderRefundId) {
            return api_response(null, '閸烆喖鎮楅崡鏃綝不能为空', 500);
        }

        $refund = OrderRefund::where('user_id', $user->id)->find($orderRefundId);
        if (!$refund) {
            return api_response(null, '售后记录不存在', 500);
        }

        $refund->express_no      = $form['express_no'] ?? '';
        $refund->express_company = $form['express_company'] ?? '';
        $refund->save();

        return api_response(null, '提交成功');
    }
}