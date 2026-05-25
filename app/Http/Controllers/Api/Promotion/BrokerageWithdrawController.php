<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\BrokerageWithdraw;
use App\Models\BrokerageUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrokerageWithdrawController extends Controller
{
    /**
     * 创建提现申请
     */
    public function create(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $price = (float) $request->input('price');
        $type = (int) $request->input('type', 10); // 10=微信 20=支付宝 30=银行卡
        $bankName = $request->input('bank_name');
        $bankAccount = $request->input('bank_account');
        $bankUser = $request->input('bank_user');

        if ($price <= 0) {
            return api_error(400, '提现金额必须大于0');
        }

        // 检查用户是否为分销员
        $brokerageUser = BrokerageUser::where('user_id', $user->id)->first();
        if (!$brokerageUser) {
            return api_error(400, '您不是分销员');
        }

        // 检查分销员状态
        if ($brokerageUser->status != 20) {
            return api_error(400, '您的分销员身份未审核通过');
        }

        // 检查可提现余额
        if ($brokerageUser->brokerage_price < $price) {
            return api_error(400, '可提现余额不足');
        }

        // 检查最小提现金额（假设最小10元）
        if ($price < 10) {
            return api_error(400, '单次提现金额不能低于10元');
        }

        // 检查银行卡提现需要的信息
        if ($type == 30) {
            if (!$bankName || !$bankAccount || !$bankUser) {
                return api_error(400, '银行卡提现需要填写银行名称、账号和开户人');
            }
        }

        // 生成提现订单号
        $orderNo = 'W' . date('YmdHis') . Str::random(6);

        // 计算手续费（假设1%）
        $serviceFee = round($price * 0.01, 2);
        $realPrice = $price - $serviceFee;

        // 创建提现记录
        $withdraw = BrokerageWithdraw::create([
            'user_id' => $user->id,
            'order_no' => $orderNo,
            'price' => $price,
            'service_fee' => $serviceFee,
            'real_price' => $realPrice,
            'type' => $type,
            'bank_name' => $bankName,
            'bank_account' => $bankAccount,
            'bank_user' => $bankUser,
            'status' => 10, // 审核中
        ]);

        // 更新分销员的可提现余额（冻结部分金额）
        $brokerageUser->brokerage_price -= $price;
        $brokerageUser->frozen_price += $price;
        $brokerageUser->save();

        return api_success($withdraw, '提现申请提交成功，请等待审核');
    }

    /**
     * 提现记录分页
     */
    public function page(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_error(401, '请先登录');
        }

        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = BrokerageWithdraw::with(['user'])
            ->where('user_id', $user->id);

        // 状态筛选：10=审核中 20=审核通过 30=审核拒绝 40=已打款
        if ($status !== null) {
            $query->where('status', (int) $status);
        }

        // 时间范围筛选
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
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
     * 获取提现详情
     */
    public function get(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return api_error(400, '缺少提现记录ID');
        }

        $withdraw = BrokerageWithdraw::with(['user'])
            ->find($id);

        if (!$withdraw) {
            return api_error(404, '提现记录不存在');
        }

        // 检查权限（只能查看自己的提现记录）
        $user = auth('sanctum')->user();
        if ($user && $withdraw->user_id != $user->id) {
            return api_error(403, '无权查看此提现记录');
        }

        return api_success($withdraw);
    }
}