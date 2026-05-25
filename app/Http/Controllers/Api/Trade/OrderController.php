<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\OrderAddress;
use App\Models\UserAddress;
use App\Models\Cart;
use App\Models\GoodsSku;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    /**
     * 缂佹挾鐣绘い鍏镐繆閹?     * uniapp: GET /trade/order/settlement?items[N].skuId=xxx&items[N].count=xxx&couponId=xxx&addressId=xxx&...
     */
    public function settlement(Request $request)
    {
        $user = auth('sanctum')->user();

        // uniapp 閺傜増鐗稿? items[N].skuId / items[N].count
        $skus = $request->input('items', []);

        // 閸忕厧顔愰弮褎鐗稿? cart_ids
        $cartIds   = $request->input('cart_ids', []);
        $addressId = $request->input('addressId') ?? $request->input('address_id');
        $couponId  = $request->input('couponId') ?? $request->input('coupon_id');
        $deliveryType = $request->input('deliveryType', 1);

        // 閼惧嘲褰囬崷鏉挎絻
        $address = null;
        if ($addressId) {
            $address = UserAddress::find($addressId);
        } else {
            $address = UserAddress::where('user_id', $user->id)
                ->where('is_default', 1)
                ->first();
        }

        $items      = [];
        $totalPrice = 0;
        $totalCount = 0;

        if (!empty($skus)) {
            // uniapp 閺傜増鐗稿蹇ョ窗閹?skuId+count 缂佹挾鐣?            foreach ($skus as $skuItem) {
                $skuId = $skuItem['skuId'] ?? 0;
                $count = (int)($skuItem['count'] ?? 1);

                $sku = GoodsSku::with('goods')->find($skuId);
                if (!$sku) continue;

                $price     = (float)$sku->price;
                $linePrice = (float)($sku->line_price ?? $price);
                $subtotal  = $price * $count;

                $items[] = [
                    'cart_id'     => 0,
                    'goods_id'    => $sku->goods_id,
                    'sku_id'      => $skuId,
                    'goods_name'  => $sku->goods->name ?? '',
                    'sku_text'    => $sku->specs_text ?? '',
                    'price'       => $price,
                    'line_price'  => $linePrice,
                    'count'       => $count,
                    'pic_url'     => $sku->goods->pic_url ?? '',
                    'subtotal'    => $subtotal,
                ];

                $totalPrice += $subtotal;
                $totalCount += $count;
            }
        } else {
            // 閺冄勭壐瀵骏绱版禒搴ゅ枠閻椻晞婧呯紒鎾剁暬
            $cartItems = Cart::with(['goods', 'sku'])
                ->where('user_id', $user->id)
                ->where('selected', true)
                ->when(!empty($cartIds), function ($q) use ($cartIds) {
                    $q->whereIn('id', $cartIds);
                })
                ->get();

            foreach ($cartItems as $cart) {
                $price     = $cart->sku ? (float)$cart->sku->price : (float)$cart->goods->price;
                $linePrice = $cart->sku ? (float)($cart->sku->line_price ?? $price) : (float)($cart->goods->line_price ?? $price);
                $subtotal  = $price * $cart->count;

                $items[] = [
                    'cart_id'     => $cart->id,
                    'goods_id'    => $cart->goods_id,
                    'sku_id'      => $cart->sku_id,
                    'goods_name'  => $cart->goods->name,
                    'sku_text'    => $cart->sku ? $cart->sku->specs_text : '',
                    'price'       => $price,
                    'line_price'  => $linePrice,
                    'count'       => $cart->count,
                    'pic_url'     => $cart->goods->pic_url,
                    'subtotal'    => $subtotal,
                ];

                $totalPrice += $subtotal;
                $totalCount += $cart->count;
            }
        }

        // 鏉╂劘鍨?        $freightPrice = $totalPrice > 99 ? 0 : 10;
        $orderPrice   = $totalPrice + $freightPrice;

        return api_success([
            'address'        => $address ? [
                'id'         => $address->id,
                'name'       => $address->name,
                'mobile'     => $address->mobile,
                'province'   => $address->province,
                'city'       => $address->city,
                'district'   => $address->district,
                'detail'     => $address->detail,
            ] : null,
            'items'          => $items,
            'goodsList'      => $items,
            'totalPrice'     => $totalPrice,
            'freightPrice'   => $freightPrice,
            'orderPrice'     => $orderPrice,
            'totalCount'     => $totalCount,
            'couponId'       => $couponId,
            'deliveryType'   => $deliveryType,
        ]);
    }

    /**
     * 缂佹挾鐣婚崯鍡楁惂閿涘牏娲块幒銉ㄥ枠娑旂増膩瀵骏绱?     * uniapp: GET /trade/order/settlement-product?spuIds=xxx&skuId=xxx&count=xxx
     */
    public function settlementProduct(Request $request)
    {
        $user      = auth('sanctum')->user();
        $spuIds    = $request->input('spuIds', []);
        $spuId     = $request->input('spu_id') ?? (is_array($spuIds) ? ($spuIds[0] ?? null) : $spuIds);
        $skuId     = $request->input('sku_id') ?? $request->input('skuId');
        $count     = (int)($request->input('count', 1));
        $addressId = $request->input('addressId') ?? $request->input('address_id');

        $spuId = (int)$spuId;

        // 閼惧嘲褰囬崯鍡楁惂
        $goods = \App\Models\Goods::find($spuId);
        if (!$goods) {
            return api_error(500, '閸熷棗鎼ф稉宥呯摠閸?);
        }

        $sku = null;
        if ($skuId) {
            $sku       = GoodsSku::find($skuId);
            $price     = (float)$sku->price;
            $linePrice = (float)($sku->line_price ?? $price);
            $skuText   = $sku->specs_text;
        } else {
            $price     = (float)$goods->price;
            $linePrice = (float)($goods->line_price ?? $price);
            $skuText   = '';
        }

        // 閼惧嘲褰囬崷鏉挎絻
        $address = null;
        if ($addressId) {
            $address = UserAddress::find($addressId);
        } else {
            $address = UserAddress::where('user_id', $user->id)
                ->where('is_default', 1)
                ->first();
        }

        $subtotal     = $price * $count;
        $freightPrice = $subtotal > 99 ? 0 : 10;

        return api_success([
            'address'      => $address ? [
                'id'         => $address->id,
                'name'       => $address->name,
                'mobile'     => $address->mobile,
                'province'   => $address->province,
                'city'       => $address->city,
                'district'   => $address->district,
                'detail'     => $address->detail,
            ] : null,
            'items'        => [[
                'spuId'     => $spuId,
                'skuId'     => $skuId,
                'goodsName' => $goods->name,
                'skuText'   => $skuText,
                'price'     => $price,
                'linePrice' => $linePrice,
                'count'     => $count,
                'picUrl'    => $goods->pic_url,
                'subtotal'  => $subtotal,
            ]],
            'goodsList'    => [[
                'spuId'     => $spuId,
                'skuId'     => $skuId,
                'goodsName' => $goods->name,
                'skuText'   => $skuText,
                'price'     => $price,
                'linePrice' => $linePrice,
                'count'     => $count,
                'picUrl'    => $goods->pic_url,
                'subtotal'  => $subtotal,
            ]],
            'totalPrice'   => $subtotal,
            'freightPrice' => $freightPrice,
            'orderPrice'   => $subtotal + $freightPrice,
            'totalCount'   => $count,
        ]);
    }

    /**
     * 閸掓稑缂撶拋銏犲礋
     * uniapp: POST /trade/order/create
     */
    public function create(Request $request)
    {
        $user = auth('sanctum')->user();

        // uniapp 閺傜増鐗稿蹇撳棘閺?        $addressId      = $request->input('addressId') ?? $request->input('address_id');
        $couponId       = $request->input('couponId') ?? $request->input('coupon_id');
        $remark         = $request->input('remark', '');
        $cartIds        = $request->input('cart_ids', []);
        $fromCart       = $request->input('from_cart', !empty($cartIds));
        $deliveryType   = $request->input('deliveryType', 1);
        $items          = $request->input('items', []);

        DB::beginTransaction();
        try {
            // 閼惧嘲褰囬崷鏉挎絻
            $address = UserAddress::find($addressId);
            if (!$address) {
                throw new \Exception('閺€鎯版彛閸︽澘娼冩稉宥呯摠閸?);
            }

            $totalPrice = 0;
            $totalCount = 0;
            $orderItems = [];

            if (!empty($items)) {
                // uniapp 閺傜増鐗稿? items 閺佹壆绮嶉惄瀛樺复娑撳宕?                foreach ($items as $item) {
                    $skuId = $item['skuId'] ?? 0;
                    $count = (int)($item['count'] ?? 1);

                    $sku = GoodsSku::with('goods')->find($skuId);
                    if (!$sku) continue;

                    $price    = (float)$sku->price;
                    $subtotal = $price * $count;

                    $orderItems[] = [
                        'goods_id'   => $sku->goods_id,
                        'sku_id'     => $skuId,
                        'goods_name' => $sku->goods->name ?? '',
                        'sku_text'   => $sku->specs_text ?? '',
                        'price'      => $price,
                        'count'      => $count,
                        'pic_url'    => $sku->goods->pic_url ?? '',
                        'subtotal'   => $subtotal,
                    ];

                    $totalPrice += $subtotal;
                    $totalCount += $count;
                }
            } elseif ($fromCart) {
                // 娴犲氦鍠橀悧鈺勬簠娑撳宕?                $cartItems = Cart::with(['goods', 'sku'])
                    ->where('user_id', $user->id)
                    ->where('selected', true)
                    ->when(!empty($cartIds), function ($q) use ($cartIds) {
                        $q->whereIn('id', $cartIds);
                    })
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new \Exception('鐠愵厾澧挎潪锔胯礋缁?);
                }

                foreach ($cartItems as $cart) {
                    $price    = $cart->sku ? (float)$cart->sku->price : (float)$cart->goods->price;
                    $subtotal = $price * $cart->count;

                    $orderItems[] = [
                        'goods_id'   => $cart->goods_id,
                        'sku_id'     => $cart->sku_id,
                        'goods_name' => $cart->goods->name,
                        'sku_text'   => $cart->sku ? $cart->sku->specs_text : '',
                        'price'      => $price,
                        'count'      => $cart->count,
                        'pic_url'    => $cart->goods->pic_url,
                        'subtotal'   => $subtotal,
                    ];

                    $totalPrice += $subtotal;
                    $totalCount += $cart->count;
                }

                Cart::whereIn('id', $cartItems->pluck('id'))->delete();
            } else {
                // 閻╁瓨甯寸拹顓濇嫳
                $spuId = $request->input('spu_id') ?? $request->input('spuId');
                $skuId = $request->input('sku_id') ?? $request->input('skuId');
                $count = (int)($request->input('count', 1));

                $goods = \App\Models\Goods::find($spuId);
                if (!$goods) throw new \Exception('閸熷棗鎼ф稉宥呯摠閸?);

                $sku = null;
                if ($skuId) {
                    $sku   = GoodsSku::find($skuId);
                    $price = (float)$sku->price;
                    $skuText = $sku->specs_text;
                } else {
                    $price   = (float)$goods->price;
                    $skuText = '';
                }

                $subtotal = $price * $count;

                $orderItems[] = [
                    'goods_id'   => $spuId,
                    'sku_id'     => $skuId,
                    'goods_name' => $goods->name,
                    'sku_text'   => $skuText,
                    'price'      => $price,
                    'count'      => $count,
                    'pic_url'    => $goods->pic_url,
                    'subtotal'   => $subtotal,
                ];

                $totalPrice = $subtotal;
                $totalCount = $count;
            }

            $freightPrice = $totalPrice > 99 ? 0 : 10;
            $orderPrice   = $totalPrice + $freightPrice;

            $order = Order::create([
                'order_no'      => date('YmdHis') . random_int(1000, 9999),
                'user_id'       => $user->id,
                'total_price'   => $totalPrice,
                'freight_price' => $freightPrice,
                'order_price'   => $orderPrice,
                'status'        => 1,
                'remark'        => $remark,
                'coupon_id'     => $couponId,
            ]);

            OrderAddress::create([
                'order_id'  => $order->id,
                'name'      => $address->name,
                'mobile'    => $address->mobile,
                'province'  => $address->province,
                'city'      => $address->city,
                'district'  => $address->district,
                'detail'    => $address->detail,
            ]);

            foreach ($orderItems as $item) {
                OrderGoods::create(array_merge($item, ['order_id' => $order->id]));
            }

            DB::commit();

            return api_success([
                'id'         => $order->id,
                'no'         => $order->order_no,
                'orderPrice' => (float)$order->order_price,
                'payAmount'  => (float)$order->order_price,
                'status'     => $order->status,
            ], '鐠併垹宕熼崚娑樼紦閹存劕濮?);
        } catch (\Exception $e) {
            DB::rollBack();
            return api_error(500, $e->getMessage());
        }
    }

    /**
     * 鐠併垹宕熺拠锔藉剰
     * uniapp: GET /trade/order/get-detail?id=xxx
     */
    public function getDetail(Request $request)
    {
        $user    = auth('sanctum')->user();
        $orderId = $request->input('id');

        $order = Order::with(['address', 'goods', 'user'])
            ->where('user_id', $user->id)
            ->find($orderId);

        if (!$order) {
            return api_error(500, '鐠併垹宕熸稉宥呯摠閸?);
        }

        return api_success($order);
    }

    /**
     * 鐠併垹宕熼崚鍡涖€?     * uniapp: GET /trade/order/page?pageNo=1&pageSize=10&status=xxx
     */
    public function page(Request $request)
    {
        $user     = auth('sanctum')->user();
        $status   = $request->input('status');
        $pageNo   = (int)($request->input('pageNo') ?? $request->input('page', 1));
        $pageSize = (int)($request->input('pageSize') ?? $request->input('page_size', 10));

        $query = Order::with(['goods'])
            ->where('user_id', $user->id);

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $total = $query->count();
        $list  = $query->orderByDesc('created_at')
            ->skip(($pageNo - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return api_success([
            'list'     => $list,
            'total'    => $total,
            'pageNo'   => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * 绾喛顓婚弨鎯版彛
     * uniapp: PUT /trade/order/receive { id }
     */
    public function receive(Request $request)
    {
        $user    = auth('sanctum')->user();
        $orderId = $request->input('id');

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_error(500, '鐠併垹宕熸稉宥呯摠閸?);
        }

        if ($order->status != 3) {
            return api_error(500, '鐠併垹宕熼悩鑸碘偓浣风瑝閸忎浇顔忕涵顔款吇閺€鎯版彛');
        }

        $order->status = 4;
        $order->save();

        return api_success(null, '閺€鎯版彛閹存劕濮?);
    }

    /**
     * 閸欐牗绉风拋銏犲礋
     * uniapp: DELETE /trade/order/cancel { id }
     */
    public function cancel(Request $request)
    {
        $user    = auth('sanctum')->user();
        $orderId = $request->input('id');

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_error(500, '鐠併垹宕熸稉宥呯摠閸?);
        }

        if ($order->status != 1) {
            return api_error(500, '鐠併垹宕熼悩鑸碘偓浣风瑝閸忎浇顔忛崣鏍ㄧХ');
        }

        $order->status        = 0;
        $order->cancel_reason = $request->input('cancelReason') ?? $request->input('cancel_reason');
        $order->save();

        return api_success(null, '鐠併垹宕熷鎻掑絿濞?);
    }

    /**
     * 閸掔娀娅庣拋銏犲礋
     * uniapp: DELETE /trade/order/delete { id }
     */
    public function delete(Request $request)
    {
        $user    = auth('sanctum')->user();
        $orderId = $request->input('id');

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_error(500, '鐠併垹宕熸稉宥呯摠閸?);
        }

        if (!in_array($order->status, [0, 4, 5])) {
            return api_error(500, '鐠併垹宕熼悩鑸碘偓浣风瑝閸忎浇顔忛崚鐘绘珟');
        }

        $order->delete();

        return api_success(null, '鐠併垹宕熷鎻掑灩闂?);
    }

    /**
     * 閼惧嘲褰囬悧鈺傜ウ鏉炪劏鎶?     * uniapp: GET /trade/order/get-express-track-list?id=xxx
     */
    public function getExpressTrackList(Request $request)
    {
        $user    = auth('sanctum')->user();
        $orderId = $request->input('id');

        $order = Order::where('user_id', $user->id)->find($orderId);
        if (!$order) {
            return api_error(500, '鐠併垹宕熸稉宥呯摠閸?);
        }

        $tracks = [
            ['time' => date('Y-m-d H:i:s', time() - 3600 * 2), 'content' => '韫囶偂娆㈠鎻掑煂鏉堜勘鈧劕绠嶅鐐舵祮鏉╂劒鑵戣箛鍐︹偓?],
            ['time' => date('Y-m-d H:i:s', time() - 3600 * 4), 'content' => '韫囶偂娆㈠韫矤閵嗘劖绻侀崷鍐插瀻閹枫劋鑵戣箛鍐︹偓鎴濆絺閸?],
            ['time' => date('Y-m-d H:i:s', time() - 3600 * 6), 'content' => '韫囶偂娆㈠鍙夊緫閺€?],
        ];

        return api_success([
            'no'             => $order->order_no,
            'expressNo'      => 'YT' . date('Ymd') . random_int(100000, 999999),
            'expressCompany' => '闂婁絻鎻箛顐︹偓?,
            'tracks'         => $tracks,
        ]);
    }

    /**
     * 鐠併垹宕熺紒鐔活吀閺佷即鍣?     * uniapp: GET /trade/order/get-count
     * 閺堢喐婀? { allCount, unpaidCount, undeliveredCount, deliveredCount, uncommentedCount, afterSaleCount }
     */
    public function getCount(Request $request)
    {
        $user = auth('sanctum')->user();

        $baseQuery = Order::where('user_id', $user->id);

        return api_success([
            'allCount'          => $baseQuery->count(),
            'unpaidCount'       => $baseQuery->clone()->where('status', 1)->count(),
            'undeliveredCount'  => $baseQuery->clone()->where('status', 2)->count(),
            'deliveredCount'    => $baseQuery->clone()->where('status', 3)->count(),
            'uncommentedCount'  => $baseQuery->clone()->where('status', 4)->count(),
            'afterSaleCount'    => $baseQuery->clone()->whereIn('status', [5, 6])->count(),
        ]);
    }

    /**
     * 閸掓稑缂撶拋銏犲礋妞ょ鐦庢禒?     * uniapp: POST /trade/order/item/create-comment
     */
    public function createComment(Request $request)
    {
        $user        = auth('sanctum')->user();
        $orderItemId = $request->input('orderItemId') ?? $request->input('order_item_id');
        $content     = $request->input('content', '');
        $score       = (int)($request->input('score', 5));
        $pics        = $request->input('pics', []);

        $orderItem = OrderGoods::find($orderItemId);
        if (!$orderItem) {
            return api_error(500, '鐠併垹宕熼崯鍡楁惂娑撳秴鐡ㄩ崷?);
        }

        $exists = Comment::where('order_item_id', $orderItemId)->exists();
        if ($exists) {
            return api_error(500, '鐠囥儱鏅㈤崫浣稿嚒鐠囧嫪鐜?);
        }

        Comment::create([
            'user_id'       => $user->id,
            'goods_id'      => $orderItem->goods_id,
            'order_item_id' => $orderItemId,
            'content'       => $content,
            'score'         => $score,
            'pics'          => is_array($pics) ? json_encode($pics) : $pics,
        ]);

        return api_success(null, '鐠囧嫪鐜幋鎰');
    }
}