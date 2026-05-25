<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * 鍒嗛〉璇勪环鍒楄〃
     */
    public function page(Request $request)
    {
        $spuId    = $request->input('spu_id');
        $page     = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);

        $query = Comment::with(['user' => function ($q) {
            $q->select('id', 'nickname', 'avatar');
        }])->where('status', 1);

        if ($spuId) {
            $query->where('goods_id', $spuId);
        }

        $total = $query->count();
        $list  = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return api_response([
            'list'      => $list,
            'total'     => $total,
            'page'      => $page,
            'page_size' => $pageSize,
        ], 'success');
    }
}