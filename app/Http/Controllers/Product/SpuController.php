<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\GoodsSpecValue;
use Illuminate\Http\Request;

class SpuController extends Controller
{
    /**
     * 鑾峰緱鍟嗗搧 SPU 鍒楄〃锛堥€氳繃 ID 鏁扮粍锛?     * GET /product/spu/list-by-ids?ids=1,2,3
     */
    public function listByIds(Request $request)
    {
        $ids = $request->input('ids', '');
        if (empty($ids)) {
            return api_response([], '鍙傛暟缂哄け', 400);
        }

        $idArray = is_array($ids) ? $ids : explode(',', $ids);
        $idArray = array_map('intval', $idArray);
        $idArray = array_filter($idArray);

        if (empty($idArray)) {
            return api_response([]);
        }

        $goodsList = Goods::whereIn('id', $idArray)
            ->where('goods_status', 10)
            ->get();

        $result = $goodsList->map(function ($goods) {
            return [
                'id' => $goods->id,
                'name' => $goods->goods_name,
                'image' => $goods->goods_image,
                'price' => (float) $goods->goods_price_min,
                'linePrice' => (float) $goods->line_price_min,
                'sales' => (int) $goods->goods_sales,
                'specType' => $goods->spec_type,
                'sellingPoint' => $goods->selling_point,
                'isUserGrade' => (bool) $goods->is_user_grade,
            ];
        });

        return api_response($result);
    }

    /**
     * 鑾峰緱鍟嗗搧 SPU 鍒嗛〉
     * GET /product/spu/page?categoryId=&search=&sortType=&pageNo=1&pageSize=10
     */
    public function page(Request $request)
    {
        $categoryId = $request->input('categoryId', 0);
        $search = $request->input('search', '');
        $sortType = $request->input('sortType', 'all');
        $pageNo = (int) $request->input('pageNo', 1);
        $pageSize = (int) $request->input('pageSize', 10);

        $query = Goods::where('goods_status', 10);

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($search)) {
            $query->where('goods_name', 'like', "%{$search}%");
        }

        // 鎺掑簭閫昏緫
        switch ($sortType) {
            case 'sales':
                $query->orderBy('goods_sales', 'desc');
                break;
            case 'price':
                $query->orderBy('goods_price_min', 'asc');
                break;
            default:
                $query->orderBy('sort', 'desc')->orderBy('goods_sales', 'desc');
                break;
        }

        $paginator = $query->paginate($pageSize, ['*'], 'page', $pageNo);

        $list = $paginator->map(function ($goods) {
            return [
                'id' => $goods->id,
                'name' => $goods->goods_name,
                'image' => $goods->goods_image,
                'price' => (float) $goods->goods_price_min,
                'linePrice' => (float) $goods->line_price_min,
                'sales' => (int) $goods->goods_sales,
                'specType' => $goods->spec_type,
                'sellingPoint' => $goods->selling_point,
                'isUserGrade' => (bool) $goods->is_user_grade,
            ];
        });

        return api_response([
            'list' => $list,
            'total' => $paginator->total(),
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * 鏌ヨ鍟嗗搧璇︽儏
     * GET /product/spu/get-detail?id=1
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id', 0);
        if (!$id) {
            return api_response(null, '鍟嗗搧ID涓嶈兘涓虹┖', 400);
        }

        $goods = Goods::with(['images', 'specs.values', 'skus', 'services'])->find($id);
        if (!$goods) {
            return api_response(null, '鍟嗗搧涓嶅瓨鍦?, 404);
        }

        $images = $goods->images->map(fn($img) => $img->image_url)->values()->toArray();

        // 鏍煎紡鍖栬鏍?        $specList = $goods->specs->map(function ($spec) {
            return [
                'specId' => $spec->id,
                'specName' => $spec->spec_name,
                'values' => $spec->values->map(function ($val) {
                    return [
                        'valueId' => $val->id,
                        'specValue' => $val->spec_value,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        // 鏍煎紡鍖?SKU
        $skuList = $goods->skus->map(function ($sku) {
            return [
                'goodsSkuId' => $sku->id,
                'skuSpecIds' => $sku->sku_spec_ids,
                'goodsPrice' => (float) $sku->goods_price,
                'linePrice' => (float) $sku->line_price,
                'stock' => (int) $sku->stock,
            ];
        })->values()->toArray();

        $result = [
            'id' => $goods->id,
            'name' => $goods->goods_name,
            'image' => $goods->goods_image,
            'images' => $images,
            'price' => (float) $goods->goods_price_min,
            'linePrice' => (float) $goods->line_price_min,
            'sales' => (int) $goods->goods_sales,
            'specType' => $goods->spec_type,
            'sellingPoint' => $goods->selling_point,
            'content' => $goods->content,
            'video' => $goods->video,
            'videoCover' => $goods->video_cover,
            'isUserGrade' => (bool) $goods->is_user_grade,
            'specList' => $specList,
            'skuList' => $skuList,
            'services' => $goods->services->map(fn($s) => ['serviceName' => $s->service_name])->values(),
        ];

        return api_response($result);
    }
}
