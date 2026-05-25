<?php

namespace App\Http\Controllers\Api\Pay;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use ApiResponse;

    /**
     * 获取钱包信息
     */
    public function get(Request $request)
    {
        $user = $request->user();

        $wallet = [
            'balance'         => (float) ($user->balance ?? 0),
            'total_recharge'  => (float) ($user->total_recharge ?? 0),
            'total_expense'   => (float) ($user->total_expense ?? 0),
            'freeze_amount'   => 0,
        ];

        return $this->success($wallet);
    }
}