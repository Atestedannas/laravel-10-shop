<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BalanceLog;
use App\Models\Order;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    /**
     * 获取收银台订单信息
     * 参数: orderId
     */
    public function orderInfo(Request $request)
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

        // 计算支付倒计时(创建时间+30分钟)        $expirationTime = null;
        if ($order->create_time) {
            $expirationTime = date('Y-m-d H:i:s', strtotime($order->create_time) + 1800);
        }

        // 用户余额
        $profile = UserProfile::where('user_id', $user->id)->first();
        $balance = (float) ($profile->balance ?? 0);

        return api_response([
            'order' => [
                'showExpiration' => true,
                'expirationTime' => $expirationTime,
                'pay_price'      => (float) $order->pay_price,
                'pay_status'     => $order->pay_status,
            ],
            'personal' => [
                'balance' => $balance,
            ],
            'paymentMethods' => [
                ['method' => 'wechat', 'is_default' => true],
                ['method' => 'alipay', 'is_default' => false],
                ['method' => 'balance', 'is_default' => false],
            ],
        ]);
    }

    /**
     * 订单支付
     * 参数: orderId, method, client
     */
    public function orderPay(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $orderId = $request->input('orderId');
        $method  = $request->input('method', 'wechat');
        $client  = $request->input('client', 'miniapp');

        if (!$orderId) {
            return api_response(null, '订单ID不能为空', 500);
        }

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_response(null, '订单不存在, 400);
        }

        if ($method === 'balance') {
            $profile = UserProfile::where('user_id', $user->id)->first();
            $balance = (float) ($profile->balance ?? 0);
            $payPrice = (float) $order->pay_price;

            if ($balance < $payPrice) {
                return api_response(null, '余额不足', 500);
            }

            // 扣减余额
            $profile->balance = $balance - $payPrice;
            $profile->save();

            // 记录余额变更日志            BalanceLog::create([
                'user_id'  => $user->id,
                'scene'    => 10,
                'money'    => -$payPrice,
                'describe' => '订单支付: . $order->order_no,
            ]);

            // 更新支付状态            $order->pay_status = 20;
            $order->save();
        } else {
            // wechat / alipay微信/支付宝模拟支付成功            $order->pay_status = 20;
            $order->save();
        }

        return api_response([
            'payment' => [
                'method' => $method,
            ],
        ]);
    }

    /**
     * 交易状态查询
     * 参数: outTradeNo 即 order_no     */
    public function tradeQuery(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        $outTradeNo = $request->input('outTradeNo');
        if (!$outTradeNo) {
            return api_response(null, '交易单号不能为空, 400);
        }

        $order = Order::where('user_id', $user->id)
            ->where('order_no', $outTradeNo)
            ->first();

        if (!$order) {
            return api_response(null, '订单不存在, 400);
        }

        return api_response([
            'isPay' => $order->pay_status == 20,
        ]);
    }
}