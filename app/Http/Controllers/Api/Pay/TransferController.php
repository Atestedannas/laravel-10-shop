<?php

namespace App\Http\Controllers\Api\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * 鍚屾杞处鐘舵€?     */
    public function sync(Request $request)
    {
        return api_success(null, '鍚屾鎴愬姛');
    }
}