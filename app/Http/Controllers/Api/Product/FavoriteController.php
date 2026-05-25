<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * 鍒嗛〉鏀惰棌鍒楄〃
     */
    public function page(Request $request)
    {
        $user     = auth('sanctum')->user();
        $page     = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);

        $query = Favorite::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }])->where('user_id', $user->id);

        $total = $query->count();
        $list  = $query->orderBy('created_at', 'desc')
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
     * 鏄惁宸叉敹钘?     */
    public function exits(Request $request)
    {
        $user  = auth('sanctum')->user();
        $spuId = $request->input('spu_id');

        $exists = Favorite::where('user_id', $user->id)
            ->where('spu_id', $spuId)
            ->exists();

        return api_response(['favorite' => $exists], 'success');
    }

    /**
     * 娣诲姞鏀惰棌
     */
    public function create(Request $request)
    {
        $user  = auth('sanctum')->user();
        $spuId = $request->input('spu_id');

        $exists = Favorite::where('user_id', $user->id)
            ->where('spu_id', $spuId)
            ->exists();

        if ($exists) {
            return api_response(null, '宸叉敹钘?, 200);
        }

        Favorite::create([
            'user_id' => $user->id,
            'spu_id'  => $spuId,
        ]);

        return api_response(null, '鏀惰棌鎴愬姛');
    }

    /**
     * 鍙栨秷鏀惰棌
     */
    public function delete(Request $request)
    {
        $user  = auth('sanctum')->user();
        $spuId = $request->input('spu_id');

        Favorite::where('user_id', $user->id)
            ->where('spu_id', $spuId)
            ->delete();

        return api_response(null, '鍙栨秷鏀惰棌鎴愬姛');
    }
}