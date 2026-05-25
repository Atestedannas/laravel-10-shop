<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * 鐠愵厾澧挎潪锕€鍨悰?     */
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
     * 濞ｈ濮炵拹顓犲⒖鏉?     */
    public function add(Request $request)
    {
        $user    = auth('sanctum')->user();
        $spuId   = $request->input('spuId') ?? $request->input('spu_id');
        $skuId   = $request->input('skuId') ?? $request->input('sku_id');
        $count   = (int) $request->input('count', 1);
        $selected = $request->input('selected', true);

        // 濡偓閺屻儲妲搁崥锕€鍑＄€涙ê婀?        $cart = Cart::where('user_id', $user->id)
            ->where('goods_id', $spuId)
            ->where('sku_id', $skuId)
            ->first();

        if ($cart) {
            $cart->count += $count;
            $cart->save();
        } else {
            Cart::create([
                'user_id'  => $user->id,
                'goods_id' => $spuId,
                'sku_id'   => $skuId,
                'count'    => $count,
                'selected' => $selected,
            ]);
        }

        return api_response(null, '濞ｈ濮為幋鎰');
    }

    /**
     * 閺囧瓨鏌婇弫浼村櫤
     */
    public function updateCount(Request $request)
    {
        $id    = $request->input('id');
        $count = (int) $request->input('count', 1);

        $cart = Cart::find($id);
        if (!$cart) {
            return api_response(null, '鐠愵厾澧挎潪锕傘€嶆稉宥呯摠閸?, 404);
        }

        $cart->count = $count;
        $cart->save();

        return api_response(null, '閺囧瓨鏌婇幋鎰');
    }

    /**
     * 閺囧瓨鏌婇柅澶夎厬閻樿埖鈧?     */
    public function updateSelected(Request $request)
    {
        $ids = $request->input('ids', []);

        // 閸忓牆鍙忛柈銊ュ絿濞戝牓鈧鑵?        $user = auth('sanctum')->user();
        Cart::where('user_id', $user->id)->update(['selected' => false]);

        // 闁鑵戦幐鍥х暰妞?        if (!empty($ids)) {
            Cart::whereIn('id', $ids)->update(['selected' => true]);
        }

        return api_response(null, '閺囧瓨鏌婇幋鎰');
    }

    /**
     * 閸掔娀娅庣拹顓犲⒖鏉烇箓銆?     */
    public function delete(Request $request)
    {
        $ids = $request->input('ids', '');

        if (is_string($ids)) {
            $ids = array_filter(array_map('intval', explode(',', $ids)));
        }

        if (empty($ids)) {
            return api_response(null, '参数错误', 500);
        }

        Cart::whereIn('id', $ids)->delete();

        return api_response(null, '删除成功');
    }
}