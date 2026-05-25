<?php

namespace App\Http\Controllers\Api\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletRechargeController extends Controller
{
    /**
     * 鍒涘缓鍏呭€艰鍗?     */
    public function create(Request $request)
    {
        $packageId = $request->input('package_id');
        $amount = $request->input('amount');

        // 妯℃嫙
        return api_success([
            'recharge_no' => 'RC' . date('YmdHis') . rand(1000, 9999),
            'amount' => $amount ?: 100,
        ]);
    }

    /**
     * 鍏呭€艰褰曞垎椤?     */
    public function page(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }
}