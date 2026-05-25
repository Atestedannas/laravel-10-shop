<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * 商品评价列表(分页)     * 参数: goodsId, page
     */
    public function list(Request $request)
    {
        $goodsId = $request->input('goodsId');
        if (!$goodsId) {
            return api_response(null, '商品ID不能为空', 500);
        }

        $page    = (int) $request->input('page', 1);
        $perPage = 10;

        $comments = Comment::with('user')
            ->where('goods_id', $goodsId)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $comments->map(function ($comment) {
            $images = $comment->images;
            if (is_string($images)) {
                $images = json_decode($images, true) ?: [];
            }

            return [
                'comment_id' => $comment->id,
                'score'      => $comment->score,
                'content'    => $comment->content,
                'images'     => $images,
                'user'       => [
                    'nickname' => $comment->user ? $comment->user->nickname : '',
                    'avatar'   => $comment->user ? $comment->user->avatar : '',
                ],
                'created_at' => $comment->created_at ? $comment->created_at->toDateTimeString() : '',
            ];
        });

        return api_response([
            'list'     => $list,
            'total'    => $comments->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }

    /**
     * 商品评价统计
     * 参数: goodsId, limit
     */
    public function listRows(Request $request)
    {
        $goodsId = $request->input('goodsId');
        if (!$goodsId) {
            return api_response(null, '商品ID不能为空', 500);
        }

        $limit = (int) $request->input('limit', 5);

        $comments = Comment::with('user')
            ->where('goods_id', $goodsId)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $list = $comments->map(function ($comment) {
            $images = $comment->images;
            if (is_string($images)) {
                $images = json_decode($images, true) ?: [];
            }

            return [
                'comment_id' => $comment->id,
                'score'      => $comment->score,
                'content'    => $comment->content,
                'images'     => $images,
                'user'       => [
                    'nickname' => $comment->user ? $comment->user->nickname : '',
                    'avatar'   => $comment->user ? $comment->user->avatar : '',
                ],
                'created_at' => $comment->created_at ? $comment->created_at->toDateTimeString() : '',
            ];
        });

        return api_response($list);
    }

    /**
     * 鐠囧嫬鍨庣紒鐔活吀
     * 参数: goodsId
     */
    public function total(Request $request)
    {
        $goodsId = $request->input('goodsId');
        if (!$goodsId) {
            return api_response(null, '商品ID不能为空', 500);
        }

        $comments = Comment::where('goods_id', $goodsId)
            ->where('status', 1)
            ->get();

        $total    = $comments->count();
        $avgScore = $total > 0 ? round($comments->avg('score'), 1) : 0;

        $scoreCounts = [
            '5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0,
        ];

        foreach ($comments as $comment) {
            $score = (string) $comment->score;
            if (isset($scoreCounts[$score])) {
                $scoreCounts[$score]++;
            }
        }

        return api_response([
            'total'       => $total,
            'avg_score'   => $avgScore,
            'scoreCounts' => $scoreCounts,
        ]);
    }
}