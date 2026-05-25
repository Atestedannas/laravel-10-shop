<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * 閸ュ墽澧栨稉濠佺炊
     * 閹恒儲鏁?file閿涘苯鐡ㄩ崒銊ュ煂 public/uploads/
     * 闂勬劕鍩?2MB閿涘本鏁幐?jpg/png/gif/webp
     * 返回: [{ data: { fileInfo: { file_id, file_path } } }]
     */
    public function image(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录, 401);
        }

        if (!$request->hasFile('file')) {
            return api_response(null, '鐠囩兘鈧瀚ㄩ弬鍥︽', 500);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return api_response(null, '閺傚洣娆㈡稉濠佺炊婢惰精瑙?, 500);
        }

        // 闂勬劕鍩?2MB
        $maxSize = 2 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            return api_response(null, '閺傚洣娆㈡径褍鐨稉宥堝厴鐡掑懓绻?MB', 500);
        }

        // 闂勬劕鍩楃猾璇茬€?        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExts)) {
            return api_response(null, '娑撳秵鏁幐浣烘畱閺傚洣娆㈢猾璇茬€烽敍灞肩矌閺€顖涘瘮 jpg/png/gif/webp', 500);
        }

        // 鐎涙ê鍋嶉崚?public/uploads/ 閹稿妫╅張鐔峰瀻閻╊喖缍?        $dateDir = date('Ymd');
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