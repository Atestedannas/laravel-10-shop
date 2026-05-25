<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * 商品分类列表(树形结构)
     * 杩斿洖: [{ category_id, name, children: [...] }]
     */
    public function list(): JsonResponse
    {
        $categories = Category::where('status', 1)
            ->orderBy('sort')
            ->get();

        $tree = $this->buildTree($categories);

        return $this->success($tree);
    }

    /**
     * 鏋勫缓鍒嗙被鏍?     */
    private function buildTree($categories, $parentId = 0): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildTree($categories, $category->id);

                $node = [
                    'category_id' => $category->id,
                    'name'        => $category->name,
                ];

                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }
}