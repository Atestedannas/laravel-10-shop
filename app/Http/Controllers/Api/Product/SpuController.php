<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use Illuminate\Http\Request;

class SpuController extends Controller
{
    /**
     * 閸掑棝銆夐崯鍡楁惂閸掓銆?     */
    public function page(Request $request)
    {
        $categoryId = $request->input('category_id');
        $keyword    = $request->input('keyword');
        $sortType   = $request->input('sort_type', 'sales');
        $page       = (int) $request->input('page', 1);
        $pageSize   = (int) $request->input('page_size', 10);

        $query = Goods::with(['images', 'skus', 'category'])
            ->where('status', 1);

        // 閹稿鍨庣猾鑽ょ摣闁绱伴崠鍛儓鐎涙劕鍨庣猾?        if ($categoryId) {
            $childIds = \App\Models\Category::where('parent_id', $categoryId)
                ->pluck('id')
                ->toArray();
            $allIds = array_merge([(int) $categoryId], $childIds);
            $query->whereIn('category_id', $allIds);
        }

        // 閸忔娊鏁拠?        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        // 閹烘帒绨?        if ($sortType === 'price') {
            $query->orderBy('price', 'asc');
        } else {
            $query->orderBy('sales', 'desc');
        }

        $total = $query->count();
        $list  = $query->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return api_response([
            'list'     => $list,
            'total'    => $total,
            'page'     => $page,
            'page_size' => $pageSize,
        ], 'success');
    }

    /**
     * 閺嶈宓?ID 閸掓銆冮懢宄板絿閸熷棗鎼?     */
    public function listByIds(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return api_response([], 'success');
        }

        $idArr = array_filter(
            array_map('intval', explode(',', $ids))
        );

        if (empty($idArr)) {
            return api_response([], 'success');
        }

        $goods = Goods::with(['images', 'skus', 'category'])
            ->where('status', 1)
            ->whereIn('id', $idArr)
            ->get();

        return api_response($goods, 'success');
    }

    /**
     * 閸熷棗鎼х拠锔藉剰
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return api_response(null, '閸欏倹鏆熺紓鍝勩亼', 500);
        }

        $goods = Goods::with([
            'images',
            'skus',
            'specs',
            'services',
            'category',
        ])->find($id);

        if (!$goods) {
            return api_response(null, '閸熷棗鎼ф稉宥呯摠閸?, 404);
        }

        // 鐠囧嫯顔戦弫浼村櫤
        $commentCount = \App\Models\Comment::where('goods_id', $id)->count();
        $goods->comment_count = $commentCount;

        return api_response($goods, 'success');
    }
}