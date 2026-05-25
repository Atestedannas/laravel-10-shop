<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 鍒嗙被鏍戠粨鏋勫垪琛?     */
    public function list()
    {
        $categories = Category::with('children.children')
            ->where('parent_id', 0)
            ->where('status', 1)
            ->orderBy('sort')
            ->get();

        return api_response($categories, 'success');
    }

    /**
     * 鏍规嵁 ID 鍒楄〃鑾峰彇鍒嗙被
     */
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

        $categories = Category::whereIn('id', $idArr)
            ->where('status', 1)
            ->get();

        return api_response($categories, 'success');
    }
}