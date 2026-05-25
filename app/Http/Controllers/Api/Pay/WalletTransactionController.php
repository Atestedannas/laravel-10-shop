<?php

namespace App\Http\Controllers\Api\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    /**
     * 閽卞寘娴佹按鍒嗛〉
     */
    public function page(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }

    /**
     * 閽卞寘娴佹按姹囨€?     */
    public function getSummary()
    {
        return api_success([
            'income_total' => 0,
            'expenditure_total' => 0,
        ]);
    }
}