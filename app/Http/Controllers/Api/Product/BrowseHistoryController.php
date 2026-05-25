<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\BrowseHistory;
use Illuminate\Http\Request;

class BrowseHistoryController extends Controller
{
    /**
     * 閸掑棝銆夊ù蹇氼潔鐠佹澘缍?     */
    public function page(Request $request)
    {
        $user     = auth('sanctum')->user();
        $page     = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);

        $query = BrowseHistory::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }])->where('user_id', $user->id);

        $total = $query->count();
        $list  = $query->orderBy('updated_at', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return api_response([
            'list'      => $list,
            'total'     => $total,
            'page'      => $page,
            'page_size' => $pageSize,
        ], 'success');
    }

    /**
     * 閸掔娀娅庨幐鍥х暰濞村繗顫嶇拋鏉跨秿
     */
    public function delete(Request $request)
    {
        $user = auth('sanctum')->user();
        $ids  = $request->input('ids');

        if (empty($ids)) {
            return api_response(null, '閸欏倹鏆熺紓鍝勩亼', 500);
        }

        $idArr = array_filter(
            array_map('intval', explode(',', $ids))
        );

        BrowseHistory::where('user_id', $user->id)
            ->whereIn('id', $idArr)
            ->delete();

        return api_response(null, '閸掔娀娅庨幋鎰');
    }

    /**
     * 濞撳懐鈹栧ù蹇氼潔鐠佹澘缍?     */
    public function clean()
    {
        $user = auth('sanctum')->user();

        BrowseHistory::where('user_id', $user->id)->delete();

        return api_response(null, '濞撳懐鈹栭幋鎰');
    }
}