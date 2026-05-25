<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * 上传文件
     * 上传文件到 public/uploads/
     * 限制大小2MB，允许 jpg/png/gif/webp
     * 返回: [{ data: { fileInfo: { file_id, file_path } } }]
     */
    public function image(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        if (!$request->hasFile('file')) {
            return api_response(null, '请选择要上传的文件', 500);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return api_response(null, '上传失败，文件类型不允许', 500);
        }

        // 限制大小 2MB
        $maxSize = 2 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            return api_response(null, '文件大小不能超过 2MB', 500);
        }

        // 闂勬劕鍩楃猾璇茬€?        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExts)) {
            return api_response(null, '娑撳秵鏁幐浣烘畱閺傚洣娆㈢猾璇茬€烽敍灞肩矌閺€顖涘瘮 jpg/png/gif/webp', 500);
        }

        // 保存到 public/uploads/ 下的日期目录
        $dateDir = date('Ymd');
        $filename = date('YmdHis') . '_' . Str::random(8) . '.' . $ext;
        $path = $file->storeAs("uploads/{$dateDir}", $filename, 'public');

        $fileId  = $path;
        $fileUrl = Storage::disk('public')->url($path);

        return api_response([
            [
                'data' => [
                    'fileInfo' => [
                        'file_id'   => $fileId,
                        'file_path' => $fileUrl,
                    ],
                ],
            ],
        ]);
    }
}