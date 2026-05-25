<?php

namespace App\Http\Controllers\Api\Member;


use App\Http\Controllers\Controller;
use App\Models\MemberSocialUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialUserController extends Controller
{

    /**
     * 閼惧嘲褰囩粈鍙ユ唉閻劍鍩涙穱鈩冧紖
     */
    public function get(Request $request)
    {
        $request->validate(['type' => 'required|string']);

        $socialUser = MemberSocialUser::where('user_id', $request->user()->id)
            ->where('type', $request->type)
            ->first();

        if (!$socialUser) {
            return api_error(500, '閺堫亞绮︾€规俺顕氱粈鍙ユ唉鐠愶箑褰?);
        }

        return api_success($socialUser);
    }

    /**
     * 缂佹垵鐣剧粈鍙ユ唉鐠愶箑褰?     */
    public function bind(Request $request)
    {
        $request->validate([
            'type'  => 'required|string',
            'code'  => 'required|string',
            'state' => 'nullable|string',
        ]);

        $userId = $request->user()->id;
        $type   = $request->type;

        // 濡偓閺屻儲妲搁崥锕€鍑＄紒鎴濈暰
        $existing = MemberSocialUser::where('user_id', $userId)->where('type', $type)->first();
        if ($existing) {
            return api_error(500, '瀹歌尙绮︾€规俺顕氶獮鍐插酱鐠愶箑褰?);
        }

        // 濡剝瀚欓幑?openid
        $openid  = 'mock_openid_' . $type . '_' . md5($request->code);
        $unionid = $type === 'weixin' ? 'mock_unionid_' . md5($request->code) : null;

        // 濡偓閺屻儴顕?openid 閺勵垰鎯佸鑼额潶閸忔湹绮悽銊﹀煕缂佹垵鐣?        $openidUsed = MemberSocialUser::where('type', $type)->where('openid', $openid)->first();
        if ($openidUsed) {
            return api_error(500, '鐠囥儳銇炴禍銈堝閸欏嘲鍑＄悮顐㈠従娴犳牜鏁ら幋椋庣拨鐎?);
        }

        $socialUser = MemberSocialUser::create([
            'user_id'  => $userId,
            'type'     => $type,
            'openid'   => $openid,
            'unionid'  => $unionid,
            'raw_data' => ['code' => $request->code],
        ]);

        return api_success($socialUser, '缂佹垵鐣鹃幋鎰');
    }

    /**
     * 鐟欙絿绮︾粈鍙ユ唉鐠愶箑褰?     */
    public function unbind(Request $request)
    {
        $request->validate([
            'type'   => 'required|string',
            'openid' => 'required|string',
        ]);

        $socialUser = MemberSocialUser::where('user_id', $request->user()->id)
            ->where('type', $request->type)
            ->where('openid', $request->openid)
            ->first();

        if (!$socialUser) {
            return api_error(500, '閺堫亝澹橀崚鎷岊嚉缂佹垵鐣剧拋鏉跨秿');
        }

        $socialUser->delete();

        return api_success(null, '鐟欙絿绮﹂幋鎰');
    }

    /**
     * 閼惧嘲褰囩拋銏ゆ濞戝牊浼呭Ο鈩冩緲
     */
    public function getSubscribeTemplateList()
    {
        return api_success([
            ['templateId' => 'mock_template_001', 'title' => '鐠併垹宕熼悩鑸碘偓渚€鈧氨鐓?],
            ['templateId' => 'mock_template_002', 'title' => '閸欐垼鎻ｉ柅姘辩叀'],
            ['templateId' => 'mock_template_003', 'title' => '闁偓濞嗛箖鈧氨鐓?],
        ]);
    }

    /**
     * 閼惧嘲褰囧顔讳繆鐏忓繒鈻兼惔蹇曠垳
     */
    public function wxaQrcode(Request $request)
    {
        $request->validate([
            'scene'     => 'required|string',
            'path'      => 'nullable|string',
            'checkPath' => 'nullable|boolean',
        ]);

        return api_success([
            'url' => 'https://example.com/mock_qrcode_' . md5($request->scene) . '.png',
        ]);
    }
}