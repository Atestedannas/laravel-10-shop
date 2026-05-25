<?php

namespace App\Http\Controllers\Api\Pay;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    /**
     * 鑾峰彇閽卞寘淇℃伅
     * uniapp: GET /pay/wallet/get
     */
    public function get(Request $request)
    {
        $user = $request->user();

        $wallet = [
            'balance'        => (float) ($user->balance ?? 0),
            'totalRecharge'  => (float) ($user->total_recharge ?? 0),
            'totalExpense'   => (float) ($user->total_expense ?? 0),
            'freezePrice'    => 0,
        ];

        return api_success($wallet);
    }
}