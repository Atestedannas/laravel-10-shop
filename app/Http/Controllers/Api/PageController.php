<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * 页面详情     * 参数: pageId
     * 返回: title, page_data閿涘湞SON鐟欙絾鐎芥稉鐑樻殶缂佸嫸绱?     */
    public function detail(Request $request)
    {
        $pageId = $request->input('pageId');
        if (!$pageId) {
            return api_response(null, '页面ID不能为空', 500);
        }

        $page = Page::where('status', 1)->find($pageId);
        if (!$page) {
            return api_response(null, '页面不存在', 400);
        }

        $pageData = $page->page_data;
        if (is_string($pageData)) {
            $pageData = json_decode($pageData, true) ?: $pageData;
        }

        return api_response([
            'title'     => $page->title,
            'page_data' => $pageData,
        ]);
    }
}