<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 閺傚洨鐝烽崚鍡欒列表     * 鏉╂柨娲栭幍鈧張澶婂瀻缁?     */
    public function categoryList()
    {
        $categories = ArticleCategory::orderBy('sort')->get();

        $list = $categories->map(function ($c) {
            return [
                'id'   => $c->id,
                'name' => $c->name,
                'sort' => $c->sort,
            ];
        });

        return api_response($list);
    }

    /**
     * 閺傚洨鐝烽崚妤勩€冮敍鍫濆瀻妞ょ绱?     * 参数: category_id(可选, page
     * 返回: title, cover, created_at
     */
    public function list(Request $request)
    {
        $categoryId = $request->input('category_id');
        $page       = (int) $request->input('page', 1);
        $perPage    = 10;

        $query = Article::where('status', 1);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $articles = $query->orderBy('sort')->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $list = $articles->map(function ($a) {
            return [
                'id'         => $a->id,
                'title'      => $a->title,
                'cover'      => $a->cover,
                'created_at' => $a->created_at ? $a->created_at->toDateTimeString() : null,
            ];
        });

        return api_response([
            'list'     => $list,
            'total'    => $articles->total(),
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }

    /**
     * 閺傚洨鐝风拠锔藉剰
     * 参数: articleId
     * 返回: title, content, created_at
     */
    public function detail(Request $request)
    {
        $articleId = $request->input('articleId');
        if (!$articleId) {
            return api_response(null, '閺傚洨鐝稩D不能为空', 500);
        }

        $article = Article::where('status', 1)->find($articleId);
        if (!$article) {
            return api_response(null, '閺傚洨鐝锋稉宥呯摠閸?, 400);
        }

        return api_response([
            'title'      => $article->title,
            'content'    => $article->content,
            'cover'      => $article->cover,
            'created_at' => $article->created_at ? $article->created_at->toDateTimeString() : null,
        ]);
    }
}