<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BalanceLog;
use Illuminate\Http\Request;

class BalanceLogController extends Controller
{
    /**
     * 余额变动明细表(分页)
     * 鎸夊綋鍓嶇敤鎴疯繃婊?     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $page    = (int) $request->input('page', 1);
        $perPage = 10;

        $logs = BalanceLog::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $logs->map(function ($log) {
            return [
                'id'         => $log->id,
                'scene'      => $log->scene,
                'money'      => $log->money,
                'describe'   => $log->describe,
                'created_at' => $log->created_at ? $log->created_at->toDateTimeString() : null,
            ];
        });

        return api_response([
            'list'     => $list,
            'total'    => $logs->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }
}