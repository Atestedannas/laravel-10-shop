<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointsLog;
use Illuminate\Http\Request;

class PointsLogController extends Controller
{
    /**
     * 绉垎鍙樺姩鏄庣粏鍒楄〃锛堝垎椤碉級
     * 鎸夊綋鍓嶇敤鎴疯繃婊?     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        $page    = (int) $request->input('page', 1);
        $perPage = 10;

        $logs = PointsLog::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $logs->map(function ($log) {
            return [
                'id'         => $log->id,
                'scene'      => $log->scene,
                'points'     => $log->points,
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