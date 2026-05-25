<?php

namespace App\Http\Controllers\Api\Member;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * 閼惧嘲褰囪ぐ鎾冲閻ц缍嶉悽銊﹀煕娣団剝浼?     */
    public function get(Request $request)
    {
        $user = $request->user();

        return api_success([
            'id'       => $user->id,
            'nickname' => $user->nickname,
            'avatar'   => $user->avatar,
            'mobile'   => $user->mobile,
            'point'    => $user->point,
            'sex'      => $user->sex,
            'birthday' => $user->birthday,
        ]);
    }

    /**
     * 閺囧瓨鏌婇悽銊﹀煕娣団剝浼?     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'nullable|string|max:50',
            'avatar'   => 'nullable|string|max:500',
            'sex'      => 'nullable|integer|in:0,1,2',
            'birthday' => 'nullable|date',
        ]);

        $request->user()->update(array_filter($validated, fn($v) => $v !== null));

        return api_success(null, '閺囧瓨鏌婇幋鎰');
    }

    /**
     * 娣囶喗鏁奸幍瀣簚閸?     */
    public function updateMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'code'   => 'required|digits:6',
        ]);

        $cacheKey    = 'sms:update_mobile:' . $request->mobile;
        $cachedCode  = Cache::get($cacheKey);

        if (!$cachedCode || $cachedCode != $request->code) {
            return api_error(500, '妤犲矁鐦夐惍渚€鏁婄拠顖涘灗瀹歌尪绻冮張?);
        }

        Cache::forget($cacheKey);

        if (User::where('mobile', $request->mobile)->where('id', '!=', $request->user()->id)->exists()) {
            return api_error(500, '鐠囥儲澧滈張鍝勫娇瀹歌尪顫﹂崗鏈电铂閻劍鍩涚紒鎴濈暰');
        }

        $request->user()->update(['mobile' => $request->mobile]);

        return api_success(null, '閹靛婧€閸欒渹鎱ㄩ弨瑙勫灇閸?);
    }

    /**
     * 瀵邦喕淇婇幒鍫熸綀娣囶喗鏁奸幍瀣簚閸?     */
    public function updateMobileByWeixin(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // 濡剝瀚欓懢宄板絿閹靛婧€閸?        $mobile = '138' . str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        if (User::where('mobile', $mobile)->where('id', '!=', $request->user()->id)->exists()) {
            return api_error(500, '鐠囥儲澧滈張鍝勫娇瀹歌尪顫﹂崗鏈电铂閻劍鍩涚紒鎴濈暰');
        }

        $request->user()->update(['mobile' => $mobile]);

        return api_success(['mobile' => $mobile], '閹靛婧€閸欒渹鎱ㄩ弨瑙勫灇閸?);
    }

    /**
     * 娣囶喗鏁肩€靛棛鐖?     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required|string|min:6',
            'newPassword' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->oldPassword, $user->password)) {
            return api_error(500, '閸樼喎鐦戦惍渚€鏁婄拠?);
        }

        $user->update(['password' => Hash::make($request->newPassword)]);

        return api_success(null, '鐎靛棛鐖滄穱顔芥暭閹存劕濮?);
    }

    /**
     * 闁插秶鐤嗙€靛棛鐖?     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile'   => 'required|regex:/^1[3-9]\d{9}$/',
            'code'     => 'required|digits:6',
            'password' => 'required|string|min:6',
        ]);

        $cacheKey   = 'sms:reset_password:' . $request->mobile;
        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode || $cachedCode != $request->code) {
            return api_error(500, '妤犲矁鐦夐惍渚€鏁婄拠顖涘灗瀹歌尪绻冮張?);
        }

        Cache::forget($cacheKey);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return api_error(500, '閻劍鍩涙稉宥呯摠閸?);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return api_success(null, '鐎靛棛鐖滈柌宥囩枂閹存劕濮?);
    }
}