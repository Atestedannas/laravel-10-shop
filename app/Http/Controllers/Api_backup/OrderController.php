<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderGoods;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * 订单状态统计     * 返回: { all, payment, delivery, received }
     */
    public function todoCounts(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $baseQuery = Order::where('user_id', $user->id)
            ->where('order_status', '!=', 20); // 排除已取消订单
        $all       = (clone $baseQuery)->count();
        $payment   = (clone $baseQuery)->where('pay_status', 10)->count();
        $delivery  = (clone $baseQuery)->where('delivery_status', 10)->count();
        $received  = (clone $baseQuery)->where('delivery_status', 20)
            ->where('receipt_status', 10)->count();

        return api_response([
            'all'      => $all,
            'payment'  => $payment,
            'delivery' => $delivery,
            'received' => $received,
        ]);
    }

    /**
     * 订单列表(按状态筛选)     * 参数: dataType, page
     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $dataType = $request->input('dataType', 'all');
        $page     = (int) $request->input('page', 1);
        $perPage  = 10;

        $query = Order::where('user_id', $user->id)
            ->where('order_status', '!=', 20);

        switch ($dataType) {
            case 'payment':
                $query->where('pay_status', 10);
                break;
            case 'delivery':
                $query->where('delivery_status', 10);
                break;
            case 'received':
                $query->where('delivery_status', 20)
                    ->where('receipt_status', 10);
                break;
        }

        $orders = $query->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $orders->map(function ($order) {
            $goods = OrderGoods::where('order_id', $order->id)->get();

            return [
                'order_id'        => $order->id,
                'order_no'        => $order->order_no,
                'order_status'    => $order->order_status,
                'pay_status'      => $order->pay_status,
                'delivery_status' => $order->delivery_status,
                'order_type'      => $order->order_type,
                'state_text'      => $this->getStateText($order),
                'create_time'     => $order->create_time,
                'total_price'     => (float) $order->total_price,
                'pay_price'       => (float) $order->pay_price,
                'delivery_type'   => $order->delivery_type,
                'goods'           => $goods->map(function ($g) {
                    return [
                        'goods_name'  => $g->goods_name,
                        'goods_image' => $g->goods_image,
                        'goods_price' => (float) $g->goods_price,
                        'total_num'   => (int) $g->total_num,
                        'goods_props' => $g->goods_props,
                    ];
                })->values(),
            ];
        });

        return api_response([
            'list'     => $list,
            'total'    => $orders->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }

    /**
     * 订单详情
     * 参数: orderId
     */
    public function detail(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $orderId = $request->input('orderId');
        if (!$orderId) {
            return api_response(null, '订单ID不能为空', 500);
        }

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_response(null, '订单不存在, 400);
        }

        $address = $order->address;
        $addressData = null;
        if ($address) {
            $addressData = [
                'name'   => $address->name,
                'phone'  => $address->phone,
                'region' => [$address->province, $address->city, $address->region],
                'detail' => $address->detail,
            ];
        }

        $goods = OrderGoods::where('order_id', $order->id)->get()->map(function ($g) {
            return [
                'goods_id'        => $g->goods_id,
                'goods_name'      => $g->goods_name,
                'goods_image'     => $g->goods_image,
                'goods_props'     => $g->goods_props,
                'goods_price'     => (float) $g->goods_price,
                'grade_goods_price' => (float) $g->goods_price,
                'total_num'       => (int) $g->total_num,
                'is_user_grade'   => $g->is_user_grade,
                'refund'          => null,
                'order_goods_id'  => $g->id,
                'delivery_status' => $order->delivery_status,
            ];
        });

        return api_response([
            'order_id'        => $order->id,
            'order_no'        => $order->order_no,
            'order_status'    => $order->order_status,
            'pay_status'      => $order->pay_status,
            'delivery_status' => $order->delivery_status,
            'receipt_status'  => $order->receipt_status,
            'order_type'      => $order->order_type,
            'delivery_type'   => $order->delivery_type,
            'state_text'      => $this->getStateText($order),
            'address'         => $addressData,
            'goods'           => $goods,
            'delivery'        => [],
            'create_time'     => $order->create_time,
            'buyer_remark'    => $order->buyer_remark,
            'total_price'     => (float) $order->total_price,
            'coupon_money'    => (float) $order->coupon_money,
            'points_money'    => (float) $order->points_money,
            'express_price'   => (float) $order->express_price,
            'update_price'    => ['symbol' => '', 'value' => ''],
            'pay_price'       => (float) $order->pay_price,
            'isAllowRefund'   => false,
        ]);
    }

    /**
     * 物流跟踪
     * 参数: orderId
     */
    public function express(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $orderId = $request->input('orderId');
        if (!$orderId) {
            return api_response(null, '订单ID不能为空', 500);
        }

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_response(null, '订单不存在, 400);
        }

        // 暂无物流信息返回空数组        return api_response([]);
    }

    /**
     * 取消订单
     * 参数: orderId
     */
    public function cancel(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $orderId = $request->input('orderId');
        if (!$orderId) {
            return api_response(null, '订单ID不能为空', 500);
        }

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_response(null, '订单不存在, 400);
        }

        $order->order_status = 20;
        $order->save();

        return api_response(null, '订单已取消);
    }

    /**
     * 确认收货
     * 参数: orderId
     */
    public function receipt(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $orderId = $request->input('orderId');
        if (!$orderId) {
            return api_response(null, '订单ID不能为空', 500);
        }

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_response(null, '订单不存在, 400);
        }

        $order->receipt_status = 20;
        $order->order_status   = 30;
        $order->save();

        return api_response(null, '收货成功);
    }

    /**
     * 获取订单状态文本描述     */
    private function getStateText($order): string
    {
        if ($order->order_status == 20) {
            return '已取消;
        }
        if ($order->order_status == 30) {
            return '已完成;
        }
        if ($order->pay_status == 10) {
            return '待付款;
        }
        if ($order->delivery_status == 10) {
            return '待发货;
        }
        if ($order->delivery_status == 20 && $order->receipt_status == 10) {
            return '待收货;
        }
        return '未知;
    }
}