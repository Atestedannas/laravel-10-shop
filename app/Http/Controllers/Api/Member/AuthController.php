<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberSocialUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{

    /**
     * 閹靛婧€閸?+ 鐎靛棛鐖滈惂璇茬秿
     */
    public function login(Request $request)
    {
        $request->validate([
            'mobile'   => 'required|regex:/^1[3-9]\d{9}$/',
            'password' => 'required|min:6',
        ]);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return api_error(500, '手机号或密码错误');
        }

        return $this->loginSuccess($user);
    }

    /**
     * 閻厺淇婃宀冪槈閻胶娅ヨぐ?     */
    public function smsLogin(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'code'   => 'required|digits:6',
        ]);

        $cacheKey = 'sms:login:' . $request->mobile;
        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode || $cachedCode != $request->code) {
            return api_error(500, '验证码错误或已过期');
        }

        Cache::forget($cacheKey);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            // 閺傛壆鏁ら幋鐤殰閸斻劍鏁為崘?            $user = User::create([
                'mobile'   => $request->mobile,
                'nickname' => '閻劍鍩? . substr($request->mobile, -4),
                'password' => Hash::make(Str::random(16)),
            ]);
        }

        return $this->loginSuccess($user);
    }

    /**
     * 閸欐垿鈧胶鐓穱锟犵崣鐠囦胶鐖?     */
    public function sendSmsCode(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'scene'  => 'required|string',
        ]);

        $code     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'sms:' . $request->scene . ':' . $request->mobile;

        Cache::put($cacheKey, $code, now()->addMinutes(5));

        Log::info('閻厺淇婃宀冪槈閻?, [
            'scene'  => $request->scene,
            'mobile' => $request->mobile,
            'code'   => $code,
        ]);

        return api_success(null, '验证码已发送');
    }

    /**
     * 閻ц鍤?     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return api_success(null, '已登出');
    }

    /**
     * 閸掗攱鏌?token
     */
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refreshToken' => 'required|string',
        ]);

        // 閺屻儲澹橀弮?token 楠炶泛鍨归梽?        $token = PersonalAccessToken::findToken($request->refreshToken);
        if ($token) {
            $token->delete();
        }

        $user        = $request->user();
        $accessToken = $user->createToken('auth')->plainTextToken;

        return api_success([
            'accessToken'  => $accessToken,
            'refreshToken' => $accessToken,
        ]);
    }

    /**
     * 缁€鍙ユ唉閻ц缍嶇捄瀹犳祮
     */
    public function socialAuthRedirect(Request $request)
    {
        $type = $request->get('type', 'weixin');

        $urls = [
            'weixin' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=xxx&redirect_uri=xxx&response_type=code&scope=snsapi_userinfo',
            'apple'  => 'https://appleid.apple.com/auth/authorize?response_type=code&client_id=xxx&redirect_uri=xxx',
        ];

        return api_success([
            'url' => $urls[$type] ?? '',
        ]);
    }

    /**
     * 缁€鍙ユ唉韫囶偅宓庨惂璇茬秿
     */
    public function socialLogin(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'code' => 'required|string',
        ]);

        $type = $request->type;

        // 濡剝瀚欓幑?openid閿涘牏鏁撴禍褏骞嗘晶鍐付鐠嬪啰鏁ょ€电懓绨查獮鍐插酱 API閿?        $openid  = 'mock_openid_' . $type . '_' . md5($request->code);
        $unionid = $type === 'weixin' ? 'mock_unionid_' . md5($request->code) : null;

        $socialUser = MemberSocialUser::where('type', $type)->where('openid', $openid)->first();

        if ($socialUser) {
            $user = $socialUser->user;
        } else {
            // 閸掓稑缂撻弬鎵暏閹?            $user = User::create([
                'nickname' => $type . '_user_' . substr($openid, -6),
                'password' => Hash::make(Str::random(16)),
            ]);

            MemberSocialUser::create([
                'user_id'  => $user->id,
                'type'     => $type,
                'openid'   => $openid,
                'unionid'  => $unionid,
                'raw_data' => ['code' => $request->code],
            ]);
        }

        return $this->loginSuccess($user);
    }

    /**
     * 瀵邦喕淇婄亸蹇曗柤鎼村繋绔撮柨顔炬瑜?     */
    public function weixinMiniAppLogin(Request $request)
    {
        $request->validate([
            'phoneCode' => 'required|string',
            'loginCode' => 'required|string',
        ]);

        // 濡剝瀚欓懢宄板絿閹靛婧€閸欏嚖绱欓悽鐔堕獓閻滎垰顣ㄩ棁鈧拫鍐暏瀵邦喕淇婇幒銉ュ經閿?        $mobile = '138' . str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
            $user = User::create([
                'mobile'   => $mobile,
                'nickname' => '瀵邦喕淇婇悽銊﹀煕' . substr($mobile, -4),
                'password' => Hash::make(Str::random(16)),
            ]);
        }

        return $this->loginSuccess($user);
    }

    /**
     * 閸掓稑缂撳顔讳繆 JS SDK 缁涙儳鎮?     */
    public function createWeixinJsapiSignature(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        return api_success([
            'appId'     => 'wx_mock_appid',
            'nonceStr'  => Str::random(16),
            'timestamp' => (string) time(),
            'signature' => md5('jsapi_ticket=mock&noncestr=xxx&timestamp=xxx&url=' . $request->url),
        ]);
    }

    /**
     * 閻ц缍嶉幋鎰缂佺喍绔撮崫宥呯安
     */
    private function loginSuccess(User $user): \Illuminate\Http\JsonResponse
    {
        $token = $user->createToken('auth')->plainTextToken;

        return api_success([
            'accessToken'  => $token,
            'refreshToken' => $token,
            'userId'       => $user->id,
            'nickname'     => $user->nickname,
            'avatar'       => $user->avatar,
            'mobile'       => $user->mobile,
        ]);
    }
}