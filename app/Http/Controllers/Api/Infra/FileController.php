<?php

namespace App\Http\Controllers\Api\Infra;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    /**
     * 娑撳﹣绱堕弬鍥︽
     * uniapp: POST /infra/file/upload
     * 閸欏倹鏆? file (multipart)
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');

        if (!$file) {
            return api_error(500, '鐠囩兘鈧瀚ㄩ弬鍥︽');
        }

        // 濡偓閺屻儲鏋冩禒璺恒亣鐏忓骏绱欓張鈧径?10MB閿?        if ($file->getSize() > 10 * 1024 * 1024) {
            return api_error(500, '閺傚洣娆㈡径褍鐨稉宥堝厴鐡掑懓绻?10MB');
        }

        // 濡偓閺屻儲鏋冩禒鍓佽閸?        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'mp4'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedTypes)) {
            return api_error(500, '娑撳秵鏁幐浣烘畱閺傚洣娆㈢猾璇茬€?);
        }

        // 鐎涙ê鍋嶉弬鍥︽
        $path = $file->store('uploads/' . date('Ymd'), 'public');
        $url = '/storage/' . $path;

        return api_success([
            'url'  => $url,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'type' => $ext,
        ], '娑撳﹣绱堕幋鎰');
    }

    /**
     * 閼惧嘲褰囨０鍕劮閸?URL閿涘牏鏁ゆ禍搴ｆ纯閹恒儰绗傛导鐘插煂 OSS/S3閿?     * uniapp: GET /infra/file/presigned-url?fileName=xxx&contentType=xxx&...
     */
    public function presignedUrl(Request $request)
    {
        $fileName    = $request->input('fileName') ?? $request->input('file_name');
        $contentType = $request->input('contentType') ?? $request->input('content_type');
        $path        = $request->input('path', 'uploads/' . date('Ymd'));

        if (!$fileName) {
            return api_error(500, '閺傚洣娆㈤崥宥勭瑝閼虫垝璐熺粚?);
        }

        // 濡剝瀚欐潻鏂挎礀閿涘牏鏁撴禍褏骞嗘晶鍐付闂嗗棙鍨?OSS/S3 SDK閿?        $uploadUrl = 'https://mock.storage.example.com/upload';
        $fileUrl   = 'https://mock.storage.example.com/files/' . $fileName;

        return api_success([
            'uploadUrl' => $uploadUrl,
            'fileUrl'   => $fileUrl,
            'method'    => 'PUT',
            'headers'   => [
                'Content-Type' => $contentType ?: 'application/octet-stream',
            ],
        ]);
    }

    /**
     * 閸掓稑缂撻弬鍥︽鐠佹澘缍?     * uniapp: POST /infra/file/create { url, name, size?, type? }
     */
    public function create(Request $request)
    {
        $url  = $request->input('url');
        $name = $request->input('name');
        $size = $request->input('size', 0);
        $type = $request->input('type', '');

        if (!$url || !$name) {
            return api_error(500, 'url 閸?name 娑撳秷鍏樻稉铏光敄');
        }

        // 濡剝瀚欓崚娑樼紦閺傚洣娆㈢拋鏉跨秿閿涘牏鏁撴禍褏骞嗘晶鍐付閸愭瑥鍙?files 鐞涱煉绱?        $fileId = random_int(1000, 9999);

        return api_success([
            'id'   => $fileId,
            'url'  => $url,
            'name' => $name,
            'size' => (int)$size,
            'type' => $type,
        ], '閺傚洣娆㈢拋鏉跨秿閸掓稑缂撻幋鎰');
    }
}