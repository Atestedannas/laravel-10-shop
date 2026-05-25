<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function info(): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->error(401, '未登录');
        }

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);

        return $this->success([
            'userId'   => $user->id,
            'nickname' => $profile->nickname ?: $user->nickname,
            'avatar'   => $profile->avatar ?: $user->avatar,
            'phone'    => $profile->phone ?: $user->mobile,
            'balance'  => (float) ($profile->balance ?? 0),
            'points'   => (int) ($profile->points ?? 0),
        ]);
    }

    public function assets(): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->error(401, '未登录');
        }

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);

        return $this->success([
            'balance' => (float) ($profile->balance ?? 0),
            'points'  => (int) ($profile->points ?? 0),
        ]);
    }

    public function bindMobile(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->error(401, '未登录');
        }

        $form  = $request->input('form', []);
        $phone = $form['phone'] ?? $request->input('phone');

        if (!$phone) {
            return $this->error(500, '手机号格式不正确');
        }

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->phone = $phone;
        $profile->save();

        return $this->success(null, '绑定成功');
    }

    public function personal(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->error(401, '未登录');
        }

        $form     = $request->input('form', []);
        $nickname = $form['nickname'] ?? $request->input('nickname');
        $avatar   = $form['avatar'] ?? $request->input('avatar');

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);

        if ($nickname !== null) {
            $profile->nickname = $nickname;
        }
        if ($avatar !== null) {
            $profile->avatar = $avatar;
        }

        $profile->save();

        return $this->success([
            'nickname' => $profile->nickname,
            'avatar'   => $profile->avatar,
        ], '更新成功');
    }
}
