<?php

namespace App\Http\Controllers\Api\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletRechargePackageController extends Controller
{
    /**
     * 鍏呭€煎椁愬垪琛?     */
    public function list()
    {
        $packages = [
            ['id' => 1, 'name' => '鍏呭€?0鍏?, 'price' => 10, 'give_price' => 0],
            ['id' => 2, 'name' => '鍏呭€?0鍏?, 'price' => 50, 'give_price' => 5],
            ['id' => 3, 'name' => '鍏呭€?00鍏?, 'price' => 100, 'give_price' => 15],
            ['id' => 4, 'name' => '鍏呭€?00鍏?, 'price' => 200, 'give_price' => 40],
        ];

        return api_success($packages);
    }
}