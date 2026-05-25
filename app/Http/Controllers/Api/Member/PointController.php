<?php

namespace App\Http\Controllers\Api\Member;


use App\Http\Controllers\Controller;
use App\Models\MemberPointRecord;
use Illuminate\Http\Request;

class PointController extends Controller
{

    /**
     * 绉垎璁板綍鍒嗛〉
     */
    public function recordPage(Request $request)
    {
        $pageNo   = (int) $request->get('pageNo', 1);
        $pageSize = (int) $request->get('pageSize', 10);

        $query = MemberPointRecord::where('user_id', $request->user()->id)
            ->orderByDesc('created_at');

        // 鎸夊鍔?娑堣€楃瓫閫?        if ($request->has('addStatus') && $request->addStatus !== '') {
            $query->where('add_status', (int) $request->addStatus);
        }

        $paginator = $query->paginate($pageSize, ['*'], 'page', $pageNo);

        return api_success([
            'total' => $paginator->total(),
            'list'  => $paginator->items(),
        ]);
    }
}