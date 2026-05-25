<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\GoodsSpecValue;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * 获取购物车列表     * 包含goods和goodsSku关联数据     */
    public function list(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $carts = Cart::where('user_id', $user->id)->get();

        if ($carts->isEmpty()) {
            return api_response([]);
        }

        $goodsIds = $carts->pluck('goods_id')->unique();
        $skuIds   = $carts->pluck('goods_sku_id')->unique();

        $goodsMap = Goods::whereIn('id', $goodsIds)->get()->keyBy('id');
        $skuMap   = GoodsSku::whereIn('id', $skuIds)->get()->keyBy('id');

        // 解析SKU的spec_ids生成skuInfo
        $allSpecValueIds = [];
        foreach ($skuMap as $sku) {
            if ($sku->sku_spec_ids) {
                $ids = explode(',', $sku->sku_spec_ids);
                foreach ($ids as $id) {
                    $allSpecValueIds[] = (int) $id;
                }
            }
        }
        $allSpecValueIds = array_unique($allSpecValueIds);
        $specValueMap = [];
        if ($allSpecValueIds) {
            $specValues = GoodsSpecValue::whereIn('id', $allSpecValueIds)->get();
            foreach ($specValues as $sv) {
                $specValueMap[$sv->id] = $sv->spec_value;
            }
        }

        $list = $carts->map(function ($cart) use ($goodsMap, $skuMap, $specValueMap) {
            $goods = $goodsMap->get($cart->goods_id);
            $sku   = $skuMap->get($cart->goods_sku_id);

            // 构建skuInfo.goods_props
            $goodsProps = [];
            if ($sku && $sku->sku_spec_ids) {
                $ids = explode(',', $sku->sku_spec_ids);
                foreach ($ids as $id) {
                    $id = (int) $id;
                    $name = $specValueMap[$id] ?? '';
                    $goodsProps[] = [
                        'value' => ['name' => $name],
                    ];
                }
            }

            return [
                'cart_id'      => $cart->id,
                'goods_id'     => $cart->goods_id,
                'goods_name'   => $goods->goods_name ?? '',
                'goods_image'  => $goods->goods_image ?? '',
                'goods_price'  => $sku ? (float) $sku->goods_price : 0,
                'line_price'   => $sku ? (float) $sku->line_price : 0,
                'total_num'    => (int) $cart->goods_num,
                'stock'        => $sku ? (int) $sku->stock : 0,
                'goods_sku_id' => $cart->goods_sku_id,
                'skuInfo'      => [
                    'goods_props' => $goodsProps,
                ],
            ];
        });

        return api_response($list->values());
    }

    /**
     * 购物车商品总数     */
    public function total(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $total = Cart::where('user_id', $user->id)->count();

        return api_response(['total' => $total]);
    }

    /**
     * 添加到购物车     * 参数: goodsId, goodsSkuId, goodsNum
     * 幂等: user_id + goods_sku_id 去重累加数量     */
    public function add(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $goodsId    = $request->input('goodsId');
        $goodsSkuId = $request->input('goodsSkuId');
        $goodsNum   = (int) $request->input('goodsNum', 1);

        if (!$goodsId || !$goodsSkuId) {
            return api_response(null, '閸欏倹鏆熸稉宥呭弿', 500);
        }

        $existing = Cart::where('user_id', $user->id)
            ->where('goods_sku_id', $goodsSkuId)
            ->first();

        if ($existing) {
            $existing->goods_num += $goodsNum;
            $existing->save();
        } else {
            Cart::create([
                'user_id'      => $user->id,
                'goods_id'     => $goodsId,
                'goods_sku_id' => $goodsSkuId,
                'goods_num'    => $goodsNum,
            ]);
        }

        return api_response(null, '添加成功');
    }

    /**
     * 更新购物车商品数量     * 参数: goodsId, goodsSkuId, goodsNum
     */
    public function update(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $goodsId    = $request->input('goodsId');
        $goodsSkuId = $request->input('goodsSkuId');
        $goodsNum   = (int) $request->input('goodsNum', 1);

        if (!$goodsId || !$goodsSkuId) {
            return api_response(null, '閸欏倹鏆熸稉宥呭弿', 500);
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('goods_id', $goodsId)
            ->where('goods_sku_id', $goodsSkuId)
            ->first();

        if (!$cart) {
            return api_response(null, '购物车商品不存在', 500);
        }

        $cart->goods_num = $goodsNum;
        $cart->save();

        return api_response(null, '更新成功');
    }

    /**
     * 删除购物车商品     * 参数: cartIds 数组     */
    public function clear(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $cartIds = $request->input('cartIds', []);

        if (empty($cartIds)) {
            return api_response(null, '请选择要删除的商品', 500);
        }

        Cart::where('user_id', $user->id)
            ->whereIn('id', $cartIds)
            ->delete();

        return api_response(null, '删除成功');
    }
}