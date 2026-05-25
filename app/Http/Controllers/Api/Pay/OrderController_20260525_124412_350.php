<?php

namespace App\Http\Controllers\Api\Pay;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * 鑾峰彇鏀粯璁㈠崟璇︽儏
     */
    public function get(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer',
            'no' => 'nullable|string',
        ]);

        $userId = $request->user()->id;

        $query = Order::where('user_id', $userId);

        if ($request->has('id')) {
            $query->where('id', $request->id);
        } elseif ($request->has('no')) {
            $query->where('order_no', $request->no);
        } else {
            return $this->error(500, '璇锋彁渚涜鍗旾D鎴栬鍗曞彿');
        }

        $order = $query->first();

        if (!$order) {
            return $this->error(500, '鏀粯璁㈠崟涓嶅瓨鍦?);
        }

        return $this->success($order);
    }

    /**
     * 鎻愪氦鏀粯
     */
    public function submit(Request $request)
    {
        $request->validate([
            'id'          => 'required|integer',
            'channelCode' => 'required|string',
        ]);

        $userId = $request->user()->id;
        $order  = Order::where('user_id', $userId)->find($request->id);

        if (!$order) {
            return $this->error(500, '璁㈠崟涓嶅瓨鍦?);
        }

        if ($order->status !== 'unpaid') {
            return $this->error(500, '璁㈠崟鐘舵€佷笉鍏佽鏀粯');
        }

        // 妯℃嫙鏀粯澶勭悊
        $payResult = [
            'id'          => $order->id,
            'payOrderNo'  => 'PAY' . date('YmdHis') . random_int(1000, 9999),
            'payAmount'   => $order->total_amount,
            'payChannel'  => $request->channelCode,
            'payTime'     => now()->toDateTimeString(),
            'status'      => 'success',
        ];

        // 鏀粯鎴愬姛鍚庢洿鏂拌鍗曠姸鎬?        $order->status = 'paid';
        $order->pay_time = now();
        $order->save();

        return $this->success($payResult, '鏀粯鎴愬姛');
    }
}