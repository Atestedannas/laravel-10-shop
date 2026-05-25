<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Order;
use App\Models\OrderGoods;
use Illuminate\Http\Request;

class OrderCommentController extends Controller
{
    /**
     * 订单评价列表     * 参数: orderId
     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $orderId = $request->input('orderId');
        if (!$orderId) {
            return api_response(null, '订单ID不能为空', 500);
        }

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_response(null, '订单不存在', 400);
        }

        // 获取订单商品信息        $orderGoods = OrderGoods::where('order_id', $order->id)->get();

        // 订单商品明细order_goods_id
        $commentedIds = Comment::where('order_id', $order->id)
            ->pluck('order_goods_id')
            ->toArray();

        // 待评价商品列表        $pendingGoods = $orderGoods->filter(function ($goods) use ($commentedIds) {
            return !in_array($goods->id, $commentedIds);
        });

        $list = $pendingGoods->map(function ($goods) {
            return [
                'order_goods_id' => $goods->id,
                'goods_id'       => $goods->goods_id,
                'goods_name'     => $goods->goods_name,
                'goods_image'    => $goods->goods_image,
                'goods_price'    => (float) $goods->goods_price,
                'total_num'      => $goods->total_num,
                'goods_props'    => $goods->goods_props,
            ];
        })->values();

        return api_response($list);
    }

    /**
     * 提交评价
     * 参数: orderId, form (goods_id => { score, content, images })
     */
    public function submit(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $orderId = $request->input('orderId');
        $form    = $request->input('form', []);

        if (!$orderId) {
            return api_response(null, '订单ID不能为空', 500);
        }

        if (empty($form)) {
            return api_response(null, '评价内容不能为空', 500);
        }

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_response(null, '订单不存在', 400);
        }

        foreach ($form as $goodsId => $item) {
            $score   = $item['score'] ?? 5;
            $content = $item['content'] ?? '';
            $images  = $item['images'] ?? [];

            // 查找对应 order_goods
            $orderGoods = OrderGoods::where('order_id', $order->id)
                ->where('goods_id', $goodsId)
                ->first();

            if (!$orderGoods) {
                continue;
            }

            // 检查是否已评价
            $exists = Comment::where('order_id', $order->id)
                ->where('order_goods_id', $orderGoods->id)
                ->exists();

            if ($exists) {
                continue;
            }

            Comment::create([
                'user_id'       => $user->id,
                'goods_id'      => $goodsId,
                'order_id'      => $order->id,
                'order_goods_id' => $orderGoods->id,
                'score'         => $score,
                'content'       => $content,
                'images'        => json_encode($images),
                'status'        => 1,
            ]);
        }

        return api_response(null, '鐠囧嫪鐜幓鎰唉成功);
    }
}