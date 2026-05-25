<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * 鑾峰彇鍦板尯鏍?     */
    public function tree()
    {
        $provinces = Region::where('level', 1)
            ->select('id', 'name', 'level', 'parent_id')
            ->get();

        return api_success($provinces);
    }
}