<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * 鑾峰緱鍟嗗搧璇勪环鍒嗛〉
     * GET /product/comment/page?spuId=1&pageNo=1&pageSize=10&type=-1
     */
    public function page(Request $request)
    {
        $spuId = (int) $request->input('spuId', 0);
        $pageNo = (int) $request->input('pageNo', 1);
        $pageSize = (int) $request->input('pageSize', 10);
        $type = (int) $request->input('type', -1);

        $query = Comment::where('goods_id', $spuId)->where('status', 1);

        // type 绛涢€夛細-1=鍏ㄩ儴, 0=濂借瘎, 1=涓瘎, 2=宸瘎
        if ($type >= 0) {
            $query->where('score', $this->scoreFilter($type));
        }

        $paginator = $query->orderBy('id', 'desc')->paginate($pageSize, ['*'], 'page', $pageNo);

        $list = $paginator->map(function ($comment) {
            return [
                'id' => $comment->id,
                'userId' => $comment->user_id,
                'nickName' => $comment->user->nickname ?? '',
                'avatar' => $comment->user->avatar ?? '',
                'content' => $comment->content,
                'score' => (int) $comment->score,
                'images' => $comment->images ? json_decode($comment->images, true) : [],
                'createTime' => $comment->created_at ? $comment->created_at->toDateTimeString() : '',
            ];
        });

        // 缁熻鍚勭被鍨嬫暟閲?        $baseQuery = Comment::where('goods_id', $spuId)->where('status', 1);
        $totalCount = (clone $baseQuery)->count();
        $goodCount = (clone $baseQuery)->where('score', '>=', 4)->count();
        $mediumCount = (clone $baseQuery)->where('score', 3)->count();
        $badCount = (clone $baseQuery)->where('score', '<=', 2)->count();

        return api_response([
            'list' => $list,
            'total' => $paginator->total(),
            'totalCount' => $totalCount,
            'goodCount' => $goodCount,
            'mediumCount' => $mediumCount,
            'badCount' => $badCount,
        ]);
    }

    private function scoreFilter(int $type)
    {
        return match ($type) {
            0 => 5,      // 濂借瘎锛氳瘎鍒嗏墺4 绠€鍖栧鐞?            1 => 3,      // 涓瘎
            2 => 1,      // 宸瘎锛氳瘎鍒嗏墹2
            default => null,
        };
    }
}