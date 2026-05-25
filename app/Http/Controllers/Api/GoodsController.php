<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\GoodsService;
use App\Models\GoodsSku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    use ApiResponse;

    /**
     * 商品列表(分页+分类+排序)     * 参数: category_id, search, sortType, page
     */
    public function list(Request $request): JsonResponse
    {
        $categoryId = $request->input('category_id');
        $search     = $request->input('search');
        $sortType   = $request->input('sortType', 'all');
        $page       = (int) $request->input('page', 1);
        $perPage    = 10;

        $query = Goods::where('goods_status', 10);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where('goods_name', 'like', "%{$search}%");
        }

        // 排序
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

        $goodsList = $query->paginate($perPage, ['*'], 'page', $page);

        $list = $goodsList->map(function (Goods $goods) {
            return [
                'goods_id'        => $goods->id,
                'goods_name'      => $goods->goods_name,
                'goods_image'     => $goods->goods_image,
                'goods_price_min' => (float) $goods->goods_price_min,
                'line_price_min'  => (float) $goods->line_price_min,
                'goods_sales'     => (int) $goods->goods_sales,
            ];
        });

        return $this->success([
            'list'     => $list,
            'total'    => $goodsList->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }

    /**
     * 商品详情(content HTML)     * 参数: goodsId
     */
    public function detail(Request $request): JsonResponse
    {
        $goodsId = $request->input('goodsId');
        if (!$goodsId) {
            return $this->error(500, '商品ID不能为空');
        }

        $goods = Goods::with(['images', 'specs.values', 'skus', 'services'])->find($goodsId);
        if (!$goods) {
            return $this->error(500, '商品不存在');
        }

        return $this->success($this->formatGoodsDetail($goods));
    }

    /**
     * 商品基本信息(content HTML)     * 参数: goodsId
     */
    public function basic(Request $request): JsonResponse
    {
        $goodsId = $request->input('goodsId');
        if (!$goodsId) {
            return $this->error(500, '商品ID不能为空');
        }

        $goods = Goods::with(['images', 'specs.values', 'skus', 'services'])->find($goodsId);
        if (!$goods) {
            return $this->error(500, '商品不存在');
        }

        $data = $this->formatGoodsDetail($goods);
        unset($data['content']);

        return $this->success($data);
    }

    /**
     * 商品规格 + SKU数据     * 参数: goodsId
     */
    public function specData(Request $request): JsonResponse
    {
        $goodsId = $request->input('goodsId');
        if (!$goodsId) {
            return $this->error(500, '商品ID不能为空');
        }

        $goods = Goods::with(['specs.values', 'skus'])->find($goodsId);
        if (!$goods) {
            return $this->error(500, '商品不存在');
        }

        return $this->success([
            'specList' => $this->formatSpecList($goods),
            'skuList'  => $this->formatSkuList($goods),
        ]);
    }

    /**
     * 获取SKU信息     * 参数: goodsSkuId
     */
    public function skuInfo(Request $request): JsonResponse
    {
        $goodsSkuId = $request->input('goodsSkuId');
        if (!$goodsSkuId) {
            return $this->error(500, 'SKU ID不能为空');
        }

        $sku = GoodsSku::find($goodsSkuId);
        if (!$sku) {
            return $this->error(500, 'SKU不存在');
        }

        return $this->success([
            'goods_sku_id' => $sku->id,
            'sku_spec_ids' => $sku->sku_spec_ids,
            'goods_price'  => (float) $sku->goods_price,
            'line_price'   => (float) $sku->line_price,
            'stock'        => (int) $sku->stock,
        ]);
    }

    /**
     * 商品服务列表     * 参数: goodsId
     */
    public function serviceList(Request $request): JsonResponse
    {
        $goodsId = $request->input('goodsId');
        if (!$goodsId) {
            return $this->error(500, '商品ID不能为空');
        }

        $services = GoodsService::where('goods_id', $goodsId)->get();

        return $this->success(
            $services->map(fn($s) => ['service_name' => $s->service_name])
        );
    }

    /**
     * 格式化商品详情数据     */
    private function formatGoodsDetail(Goods $goods): array
    {
        $images = $goods->images->map(fn($img) => $img->image_url)->values()->toArray();

        return [
            'goods_id'        => $goods->id,
            'goods_name'      => $goods->goods_name,
            'goods_image'     => $goods->goods_image,
            'goods_images'    => $images,
            'goods_price_min' => (float) $goods->goods_price_min,
            'line_price_min'  => (float) $goods->line_price_min,
            'goods_sales'     => (int) $goods->goods_sales,
            'spec_type'       => $goods->spec_type,
            'selling_point'   => $goods->selling_point,
            'content'         => $goods->content,
            'video'           => $goods->video,
            'videoCover'      => $goods->video_cover,
            'is_user_grade'   => $goods->is_user_grade,
            'specList'        => $this->formatSpecList($goods),
            'skuList'         => $this->formatSkuList($goods),
            'services'        => $goods->services->map(fn($s) => ['service_name' => $s->service_name])->values(),
        ];
    }

    /**
     * 格式化规格列表     * [{ spec_id, spec_name, values: [{ value_id, spec_value }] }]
     */
    private function formatSpecList(Goods $goods): array
    {
        return $goods->specs->map(function ($spec) {
            return [
                'spec_id'   => $spec->id,
                'spec_name' => $spec->spec_name,
                'values'    => $spec->values->map(function ($val) {
                    return [
                        'value_id'   => $val->id,
                        'spec_value' => $val->spec_value,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }

    /**
     * 格式化SKU列表     * [{ goods_sku_id, sku_spec_ids, goods_price, line_price, stock }]
     */
    private function formatSkuList(Goods $goods): array
    {
        return $goods->skus->map(function ($sku) {
            return [
                'goods_sku_id' => $sku->id,
                'sku_spec_ids' => $sku->sku_spec_ids,
                'goods_price'  => (float) $sku->goods_price,
                'line_price'   => (float) $sku->line_price,
                'stock'        => (int) $sku->stock,
            ];
        })->values()->toArray();
    }
}