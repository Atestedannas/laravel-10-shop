<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use App\Models\OrderRefund;
use App\Models\OrderGoods;
use Illuminate\Http\Request;

class AfterSaleController extends Controller
{
    /**
     * 鍞悗鍒嗛〉
     */
    public function page(Request $request)
    {
        $user = auth('sanctum')->user();
        $status = $request->input('status');
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);

        $query = OrderRefund::with(['order', 'orderGoods'])
            ->where('user_id', $user->id);

        if ($status !== null) {
            $query->where('status', $status);
        }

        $total = $query->count();
        $list = $query->orderBy('created_at', 'desc')
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
     * 鍒涘缓鍞悗
     */
    public function create(Request $request)
    {
        $user = auth('sanctum')->user();
        $orderItemId = $request->input('order_item_id');
        $refundType = $request->input('refund_type', 1); // 1=浠呴€€娆? 2=閫€璐ч€€娆?        $refundAmount = $request->input('refund_amount');
        $refundReason = $request->input('refund_reason');
        $refundDesc = $request->input('refund_desc');
        $pics = $request->input('pics', []);

        $orderItem = OrderGoods::with('order')->find($orderItemId);
        if (!$orderItem) {
            return api_error(404, '璁㈠崟鍟嗗搧涓嶅瓨鍦?);
        }

        // 妫€鏌ヨ鍗曟槸鍚﹀睘浜庡綋鍓嶇敤鎴?        if ($orderItem->order->user_id != $user->id) {
            return api_error(403, '鏃犳潈鎿嶄綔');
        }

        // 妫€鏌ユ槸鍚﹀凡鐢宠鍞悗
        $exists = OrderRefund::where('order_item_id', $orderItemId)
            ->whereIn('status', [0, 1, 2]) // 寰呭鐞?澶勭悊涓?宸插悓鎰?            ->exists();
        if ($exists) {
            return api_error(400, '璇ュ晢鍝佸凡鐢宠鍞悗锛岃鍕块噸澶嶆彁浜?);
        }

        $refund = OrderRefund::create([
            'refund_no' => 'RF' . date('YmdHis') . rand(1000, 9999),
            'user_id' => $user->id,
            'order_id' => $orderItem->order_id,
            'order_item_id' => $orderItemId,
            'refund_type' => $refundType,
            'refund_amount' => $refundAmount ?: $orderItem->subtotal,
            'refund_reason' => $refundReason,
            'refund_desc' => $refundDesc,
            'pics' => json_encode($pics),
            'status' => 0, // 寰呭鐞?        ]);

        return api_success(['refund_id' => $refund->id], '鍞悗鐢宠鎻愪氦鎴愬姛');
    }

    /**
     * 鍞悗璇︽儏
     */
    public function get(Request $request)
    {
        $refundId = $request->input('id');
        $refund = OrderRefund::with(['order', 'orderGoods', 'logs'])
            ->find($refundId);

        if (!$refund) {
            return api_error(404, '鍞悗璁板綍涓嶅瓨鍦?);
        }

        return api_success($refund);
    }

    /**
     * 鍙栨秷鍞悗
     */
    public function cancel(Request $request)
    {
        $refundId = $request->input('id');
        $refund = OrderRefund::find($refundId);

        if (!$refund) {
            return api_error(404, '鍞悗璁板綍涓嶅瓨鍦?);
        }

        // 鍙湁寰呭鐞嗙殑鍞悗鍙互鍙栨秷
        if ($refund->status != 0) {
            return api_error(400, '褰撳墠鐘舵€佷笉鍏佽鍙栨秷');
        }

        $refund->status = 4; // 宸插彇娑?        $refund->save();

        return api_success(null, '鍞悗宸插彇娑?);
    }

    /**
     * 濉啓閫€璐х墿娴?     */
    public function delivery(Request $request)
    {
        $refundId = $request->input('id');
        $expressCompany = $request->input('express_company');
        $expressNo = $request->input('express_no');

        $refund = OrderRefund::find($refundId);
        if (!$refund) {
            return api_error(404, '鍞悗璁板綍涓嶅瓨鍦?);
        }

        // 鍙湁宸插悓鎰忛€€璐х殑鍞悗鎵嶈兘濉啓鐗╂祦
        if ($refund->status != 2) {
            return api_error(400, '褰撳墠鐘舵€佷笉鍏佽濉啓鐗╂祦');
        }

        $refund->express_company = $expressCompany;
        $refund->express_no = $expressNo;
        $refund->status = 3; // 寰呴€€娆?        $refund->save();

        return api_success(null, '鐗╂祦淇℃伅宸叉彁浜?);
    }

    /**
     * 鍞悗鏃ュ織
     */
    public function logList(Request $request)
    {
        $refundId = $request->input('refund_id');
        $refund = OrderRefund::find($refundId);

        if (!$refund) {
            return api_error(404, '鍞悗璁板綍涓嶅瓨鍦?);
        }

        // 妯℃嫙鏃ュ織
        $logs = [
            [
                'time' => $refund->created_at->format('Y-m-d H:i:s'),
                'content' => '鎻愪氦鍞悗鐢宠',
            ],
        ];

        if ($refund->status >= 1) {
            $logs[] = [
                'time' => date('Y-m-d H:i:s', strtotime($refund->created_at) + 3600),
                'content' => '鍟嗗宸插彈鐞?,
            ];
        }

        if ($refund->status >= 2) {
            $logs[] = [
                'time' => date('Y-m-d H:i:s', strtotime($refund->created_at) + 7200),
                'content' => '鍟嗗鍚屾剰閫€娆?,
            ];
        }

        if ($refund->express_company) {
            $logs[] = [
                'time' => date('Y-m-d H:i:s', strtotime($refund->created_at) + 10800),
                'content' => '宸插彂璐э紝鐗╂祦鍗曞彿锛? . $refund->express_no,
            ];
        }

        if ($refund->status == 5) {
            $logs[] = [
                'time' => date('Y-m-d H:i:s', strtotime($refund->created_at) + 14400),
                'content' => '閫€娆炬垚鍔?,
            ];
        }

        return api_success($logs);
    }
}