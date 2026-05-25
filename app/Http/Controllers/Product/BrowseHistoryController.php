<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\BrowseHistory;
use Illuminate\Http\Request;

class BrowseHistoryController extends Controller
{
    /**
     * 鑾峰緱鍟嗗搧娴忚璁板綍鍒嗛〉
     * GET /product/browse-history/page?pageNo=1&pageSize=10
     */
    public function page(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        $pageNo = (int) $request->input('pageNo', 1);
        $pageSize = (int) $request->input('pageSize', 10);

        $paginator = BrowseHistory::where('user_id', $user->id)
            ->with('goods')
            ->orderBy('id', 'desc')
            ->paginate($pageSize, ['*'], 'page', $pageNo);

        $list = $paginator->map(function ($history) {
            $goods = $history->goods;
            return [
                'id' => $history->id,
                'spuId' => $history->goods_id,
                'spuName' => $goods->goods_name ?? '',
                'spuImage' => $goods->goods_image ?? '',
                'spuPrice' => $goods ? (float) $goods->goods_price_min : 0,
                'createTime' => $history->created_at ? $history->created_at->toDateTimeString() : '',
            ];
        });

        return api_response([
            'list' => $list,
            'total' => $paginator->total(),
        ]);
    }

    /**
     * 鍒犻櫎鍟嗗搧娴忚璁板綍
     * DELETE /product/browse-history/delete  { spuIds: [1, 2] }
     */
    public function delete(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        $spuIds = $request->input('spuIds', []);
        if (empty($spuIds)) {
            return api_response(null, '鍙傛暟缂哄け', 400);
        }

        BrowseHistory::where('user_id', $user->id)
            ->whereIn('goods_id', $spuIds)
            ->delete();

        return api_response(null, '鍒犻櫎鎴愬姛');
    }

    /**
     * 娓呯┖鍟嗗搧娴忚璁板綍
     * DELETE /product/browse-history/clean
     */
    public function clean(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        BrowseHistory::where('user_id', $user->id)->delete();

        return api_response(null, '娓呯┖鎴愬姛');
    }
}