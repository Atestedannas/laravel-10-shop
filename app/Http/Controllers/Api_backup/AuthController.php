<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * 鎵嬫満閸?+ 楠岃瘉閻浣烘ヨぐ?     */
    public function login(Request $request): JsonResponse
    {
        $phone = $request->input('form.phone');
        if (!$phone) {
            return $this->error(500, '鎵嬫満閸欒渹绗夐懗鎴掕礋绌);
        }

        $user = User::where('mobile', $phone)->first();
        if (!$user) {
            $user = User::create([
                'mobile'   => $phone,
                'nickname' => '鐢ㄦ埛' . substr($phone, -4),
                'password' => bcrypt(Str::random(16)),
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'token'  => $token,
            'userId' => $user->id,
        ]);
    }

    /**
     * 寰淇″皬绋嬪簭鐧诲綍     */
    public function loginMpWx(Request $request): JsonResponse
    {
        $mockPhone = '1380000' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $user = User::where('mobile', $mockPhone)->first();
        if (!$user) {
            $user = User::create([
                'mobile'   => $mockPhone,
                'nickname' => '瀵邦喕淇婄敤鎴' . rand(1000, 9999),
                'password' => bcrypt(Str::random(16)),
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'token'  => $token,
            'userId' => $user->id,
        ]);
    }

    /**
     * 瀵邦喕淇婃墜鏈洪崣閿嬪房閺夊啰娅ヨぐ?     */
    public function loginMpWxMobile(Request $request): JsonResponse
    {
        $phone = $request->input('form.phone', $request->input('phone'));

        if ($phone) {
            $user = User::where('mobile', $phone)->first();
            if (!$user) {
                $user = User::create([
                    'mobile'   => $phone,
                    'nickname' => '瀵邦喕淇婄敤鎴' . substr($phone, -4),
                    'password' => bcrypt(Str::random(16)),
                ]);
            }
        } else {
            $mockPhone = '1380000' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $user = User::where('mobile', $mockPhone)->first();
            if (!$user) {
                $user = User::create([
                    'mobile'   => $mockPhone,
                    'nickname' => '瀵邦喕淇婄敤鎴' . rand(1000, 9999),
                    'password' => bcrypt(Str::random(16)),
                ]);
            }
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'token'  => $token,
            'userId' => $user->id,
        ]);
    }

    /**
     * 閺勵垰鎯侀棁鈧鐟曚礁锝炲啓鐭╅樀绉板ご鍍     */
    public function isPersonalMpweixin(Request $request): JsonResponse
    {
        return $this->success([
            'needPersonal' => false,
        ]);
    }
}