<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 鍒嗙被鍒楄〃锛堟爲褰級
     * GET /product/category/list
     */
    public function list(Request $request)
    {
        $categories = Category::where('status', 1)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();

        $tree = $this->buildTree($categories, 0);

        return api_response($tree);
    }

    /**
     * 閫氳繃 ID 鍒楄〃鑾峰彇鍒嗙被
     * GET /product/category/list-by-ids?ids=1,2,3
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

        $categories = Category::whereIn('id', $idArray)
            ->where('status', 1)
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'parentId' => (int) $cat->parent_id,
                    'sort' => (int) $cat->sort,
                ];
            });

        return api_response($categories);
    }

    private function buildTree(array $items, int $parentId = 0): array
    {
        $tree = [];
        foreach ($items as $item) {
            if ((int) $item['parent_id'] === $parentId) {
                $children = $this->buildTree($items, (int) $item['id']);
                $node = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'parentId' => (int) $item['parent_id'],
                    'sort' => (int) ($item['sort'] ?? 0),
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