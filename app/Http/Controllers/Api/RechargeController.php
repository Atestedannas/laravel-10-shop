<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recharge;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class RechargeController extends Controller
{
    /**
     * 充值套餐列表     * 返回: { balance, plans }
     */
    public function center()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $profile = UserProfile::where('user_id', $user->id)->first();
        $balance = $profile ? (float) $profile->balance : 0;

        $plans = [
            ['plan_id' => 1, 'money' => 10, 'give_money' => 0, 'is_recommend' => false],
            ['plan_id' => 2, 'money' => 50, 'give_money' => 5, 'is_recommend' => true],
            ['plan_id' => 3, 'money' => 100, 'give_money' => 10, 'is_recommend' => false],
        ];

        return api_response([
            'balance' => $balance,
            'plans'   => $plans,
        ]);
    }

    /**
     * 提交充值     * 参数: form.plan_id
     * 返回: recharge_id, order_no
     */
    public function submit(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $planId = $request->input('form.plan_id');
        if (!$planId) {
            return api_response(null, '充值金额不能为空', 500);
        }

        // 支付渠道描述        $plans = [
            1 => ['money' => 10, 'give_money' => 0],
            2 => ['money' => 50, 'give_money' => 5],
            3 => ['money' => 100, 'give_money' => 10],
        ];

        if (!isset($plans[$planId])) {
            return api_response(null, '支付金额不能小于0', 400);
        }

        $plan  = $plans[$planId];
        $orderNo = date('YmdHis') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $recharge = Recharge::create([
            'user_id'    => $user->id,
            'order_no'   => $orderNo,
            'plan_id'    => $planId,
            'money'      => $plan['money'],
            'pay_status' => 0,
        ]);

        return api_response([
            'recharge_id' => $recharge->id,
            'order_no'    => $orderNo,
        ]);
    }

    /**
     * 閸忓懎鈧吋鐓￠崡?     * 参数: outTradeNo
     * 返回: isPay
     */
    public function tradeQuery(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $outTradeNo = $request->input('outTradeNo');
        if (!$outTradeNo) {
            return api_response(null, '订单号不能为空', 500);
        }

        $recharge = Recharge::where('user_id', $user->id)
            ->where('order_no', $outTradeNo)
            ->first();

        $isPay = $recharge && $recharge->pay_status == 1;

        return api_response([
            'isPay' => $isPay,
        ]);
    }

    /**
     * 充值订单列表
     * 返回:recharge 列表
     */
    public function orderList(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $page    = (int) $request->input('page', 1);
        $perPage = 10;

        $recharges = Recharge::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $recharges->map(function ($r) {
            return [
                'id'         => $r->id,
                'order_no'   => $r->order_no,
                'plan_id'    => $r->plan_id,
                'money'      => (float) $r->money,
                'pay_status' => $r->pay_status,
                'pay_method' => $r->pay_method,
                'pay_time'   => $r->pay_time ? $r->pay_time->toDateTimeString() : null,
                'created_at' => $r->created_at ? $r->created_at->toDateTimeString() : null,
            ];
        });

        return api_response([
            'list'     => $list,
            'total'    => $recharges->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }
}