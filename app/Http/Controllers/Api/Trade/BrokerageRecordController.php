<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrokerageRecordController extends Controller
{
    /**
     * 鍒嗛攢璁板綍鍒嗛〉
     */
    public function page(Request $request)
    {
        return api_success([
            'list' => [],
            'total' => 0,
        ]);
    }

    /**
     * 鑾峰彇鍟嗗搧浣ｉ噾
     */
    public function getProductBrokeragePrice(Request $request)
    {
        return api_success([
            'brokerage_price' => 0,
        ]);
    }
}