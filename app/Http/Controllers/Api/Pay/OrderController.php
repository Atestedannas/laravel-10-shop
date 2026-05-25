<?php

namespace App\Http\Controllers\Api\Pay;


use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * 閼惧嘲褰囬弨顖欑帛鐠併垹宕熸穱鈩冧紖
     * uniapp: GET /pay/order/get?id=xxx 閹?no=xxx
     */
    public function get(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer',
            'no' => 'nullable|string',
        ]);

        if ($request->has('id')) {
            $order = Order::find($request->id);
        } elseif ($request->has('no')) {
            $order = Order::where('order_no', $request->no)->first();
        } else {
            return api_error(500, '鐠囬攱褰佹笟娑滎吂閸楁椌D閹存牞顓归崡鏇炲娇');
        }

        if (!$order) {
            return api_error(500, '閺€顖欑帛鐠併垹宕熸稉宥呯摠閸?);
        }

        return api_success([
            'id'            => $order->id,
            'no'            => $order->order_no,
            'orderPrice'    => (float) $order->order_price,
            'status'        => $order->status,
            'subject'       => '閸熷棗鎼х拋銏犲礋',
            'createTime'    => $order->created_at ? $order->created_at->toDateTimeString() : null,
            'payAmount'     => (float) ($order->pay_amount ?? $order->order_price),
        ]);
    }

    /**
     * 閹绘劒姘﹂弨顖欑帛
     * uniapp: POST /pay/order/submit { id, channelCode, ... }
     */
    public function submit(Request $request)
    {
        $request->validate([
            'id'          => 'required|integer',
            'channelCode' => 'required|string',
        ]);

        $order = Order::find($request->id);

        if (!$order) {
            return api_error(500, '鐠併垹宕熸稉宥呯摠閸?);
        }

        if ($order->status !== 'unpaid' && $order->status != 1) {
            return api_error(500, '鐠併垹宕熼悩鑸碘偓浣风瑝閸忎浇顔忛弨顖欑帛');
        }

        $channelCode = $request->channelCode;

        // 閻㈢喐鍨氶弨顖欑帛閸欏倹鏆熼敍鍫濅簳娣団€崇毈缁嬪绨弨顖欑帛娴ｈ法鏁?jsapi閿?        $payData = [
            'timeStamp' => (string) time(),
            'nonceStr'  => substr(md5(uniqid()), 0, 16),
            'package'   => 'prepay_id=mock_' . uniqid(),
            'signType'  => 'MD5',
            'paySign'   => md5('mock_sign_' . time()),
        ];

        // 閺囧瓨鏌婄拋銏犲礋閻樿埖鈧?        $order->status    = 'paid';
        $order->pay_time  = now();
        $order->pay_amount = $order->order_price;
        $order->save();

        return api_success([
            'id'          => $order->id,
            'no'          => $order->order_no,
            'payOrderNo'  => 'PAY' . date('YmdHis') . random_int(1000, 9999),
            'channelCode' => $channelCode,
            'payData'     => $payData,
            'payAmount'   => (float) ($order->pay_amount ?? $order->order_price),
        ], '閺€顖欑帛瀹稿弶褰佹禍?);
    }
}