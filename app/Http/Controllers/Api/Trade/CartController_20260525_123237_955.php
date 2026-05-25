<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * 璐墿杞﹀垪琛?     */
    public function list()
    {
        $user = auth('sanctum')->user();

        $carts = Cart::with(['goods' => function ($q) {
            $q->with(['images', 'skus']);
        }])->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return api_response($carts, 'success');
    }

    /**
     * 娣诲姞璐墿杞?     */
    public function add(Request $request)
    {
        $user     = auth('sanctum')->user();
        $spuId    = $request->input('spu_id');
        $skuId    = $request->input('sku_id');
        $count    = (int) $request->input('count', 1);
        $selected = $request->input('selected', true);

        // 妫€鏌ユ槸鍚﹀凡瀛樺湪锛堜娇鐢?goods_sku_id 鍖归厤锛?        $cart = Cart::where('user_id', $user->id)
            ->where('goods_id', $spuId)
            ->where('goods_sku_id', $skuId)
            ->first();

        if ($cart) {
            $cart->goods_num += $count;
            $cart->selected = $selected;
            $cart->save();
        } else {
            Cart::create([
                'user_id'      => $user->id,
                'goods_id'     => $spuId,
                'goods_sku_id'  => $skuId,
                'goods_num'    => $count,
                'selected'     => $selected,
            ]);
        }

        return api_response(null, '娣诲姞鎴愬姛');
    }

    /**
     * 鏇存柊鏁伴噺
     */
    public function updateCount(Request $request)
    {
        $id    = $request->input('id');
        $count = (int) $request->input('count', 1);

        $cart = Cart::find($id);
        if (!$cart) {
            return api_response(null, '璐墿杞﹂」涓嶅瓨鍦?, 404);
        }

        $cart->goods_num = $count;
        $cart->save();

        return api_response(null, '鏇存柊鎴愬姛');
    }

    /**
     * 鏇存柊閫変腑鐘舵€?     */
    public function updateSelected(Request $request)
    {
        $user = auth('sanctum')->user();
        $ids  = $request->input('ids', []);

        // 鍏堝叏閮ㄥ彇娑堥€変腑
        Cart::where('user_id', $user->id)->update(['selected' => false]);

        // 閫変腑鎸囧畾椤?        if (!empty($ids)) {
            Cart::whereIn('id', $ids)->update(['selected' => true]);
        }

        return api_response(null, '鏇存柊鎴愬姛');
    }

    /**
     * 鍒犻櫎璐墿杞﹂」
     */
    public function delete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return api_response(null, '鍙傛暟缂哄け', 500);
        }

        Cart::whereIn('id', $ids)->delete();

        return api_response(null, '鍒犻櫎鎴愬姛');
    }
}