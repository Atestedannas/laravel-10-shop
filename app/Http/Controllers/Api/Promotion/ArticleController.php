<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 鑾峰彇鏂囩珷璇︽儏
     */
    public function get(Request $request)
    {
        $id = $request->input('id');

        $article = Article::find($id);
        if (!$article) {
            return api_error(404, '鏂囩珷涓嶅瓨鍦?);
        }

        return api_success($article);
    }
}