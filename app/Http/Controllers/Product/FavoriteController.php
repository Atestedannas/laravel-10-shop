<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * 鑾峰緱鍟嗗搧鏀惰棌鍒嗛〉
     * GET /product/favorite/page?pageNo=1&pageSize=10
     */
    public function page(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        $pageNo = (int) $request->input('pageNo', 1);
        $pageSize = (int) $request->input('pageSize', 10);

        $paginator = Favorite::where('user_id', $user->id)
            ->with('goods')
            ->orderBy('id', 'desc')
            ->paginate($pageSize, ['*'], 'page', $pageNo);

        $list = $paginator->map(function ($fav) {
            $goods = $fav->goods;
            return [
                'id' => $fav->id,
                'spuId' => $fav->goods_id,
                'spuName' => $goods->goods_name ?? '',
                'spuImage' => $goods->goods_image ?? '',
                'spuPrice' => $goods ? (float) $goods->goods_price_min : 0,
                'createTime' => $fav->created_at ? $fav->created_at->toDateTimeString() : '',
            ];
        });

        return api_response([
            'list' => $list,
            'total' => $paginator->total(),
        ]);
    }

    /**
     * 妫€鏌ユ槸鍚︽敹钘忚繃鍟嗗搧
     * GET /product/favorite/exits?spuId=1
     */
    public function exits(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        $spuId = (int) $request->input('spuId', 0);
        if (!$spuId) {
            return api_response(null, '鍙傛暟缂哄け', 400);
        }

        $exists = Favorite::where('user_id', $user->id)->where('goods_id', $spuId)->exists();

        return api_response(['exists' => $exists]);
    }

    /**
     * 娣诲姞鍟嗗搧鏀惰棌
     * POST /product/favorite/create  { spuId }
     */
    public function create(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        $spuId = (int) $request->input('spuId', 0);
        if (!$spuId) {
            return api_response(null, '鍙傛暟缂哄け', 400);
        }

        $exists = Favorite::where('user_id', $user->id)->where('goods_id', $spuId)->first();
        if ($exists) {
            return api_response(null, '宸叉敹钘?, 0);
        }

        Favorite::create([
            'user_id' => $user->id,
            'goods_id' => $spuId,
        ]);

        return api_response(null, '鏀惰棌鎴愬姛');
    }

    /**
     * 鍙栨秷鍟嗗搧鏀惰棌
     * DELETE /product/favorite/delete  { spuId }
     */
    public function delete(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '鏈櫥褰?, 401);
        }

        $spuId = (int) $request->input('spuId', 0);
        if (!$spuId) {
            return api_response(null, '鍙傛暟缂哄け', 400);
        }

        Favorite::where('user_id', $user->id)->where('goods_id', $spuId)->delete();

        return api_response(null, '鍙栨秷鎴愬姛');
    }
}