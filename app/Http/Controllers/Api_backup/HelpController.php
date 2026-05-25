<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Help;

class HelpController extends Controller
{
    /**
     * 甯姪涓績鍒楄〃
     * 杩斿洖: title, content, sort
     */
    public function list()
    {
        $helps = Help::where('status', 1)
            ->orderBy('sort')
            ->get();

        $list = $helps->map(function ($h) {
            return [
                'id'      => $h->id,
                'title'   => $h->title,
                'content' => $h->content,
                'sort'    => $h->sort,
            ];
        });

        return api_response($list);
    }
}