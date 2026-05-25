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

     * 手机号+验证码登录     */

    public function login(Request $request): JsonResponse

    {

        $phone = $request->input('form.phone');

        if (!$phone) {

            return $this->error(500, '手机号不能为空');

        }

        $user = User::where('mobile', $phone)->first();

        if (!$user) {

            $user = User::create([

                'mobile'   => $phone,

                'nickname' => '用户' . substr($phone, -4),

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

     * 微信小程序登录     */

    public function loginMpWx(Request $request): JsonResponse

    {

        $mockPhone = '1380000' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $user = User::where('mobile', $mockPhone)->first();

        if (!$user) {

            $user = User::create([

                'mobile'   => $mockPhone,

                'nickname' => '微信用户' . rand(1000, 9999),

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

     * 微信手机号授权登录     */

    public function loginMpWxMobile(Request $request): JsonResponse

    {

        $phone = $request->input('form.phone', $request->input('phone'));

        if ($phone) {

            $user = User::where('mobile', $phone)->first();

            if (!$user) {

                $user = User::create([

                    'mobile'   => $phone,

                    'nickname' => '微信用户' . substr($phone, -4),

                    'password' => bcrypt(Str::random(16)),

                ]);

            }

        } else {

            $mockPhone = '1380000' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $user = User::where('mobile', $mockPhone)->first();

            if (!$user) {

                $user = User::create([

                    'mobile'   => $mockPhone,

                    'nickname' => '微信用户' . rand(1000, 9999),

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

     * 是否需要填写昵称头像     */

    public function isPersonalMpweixin(Request $request): JsonResponse

    {

        return $this->success([

            'needPersonal' => false,

        ]);

    }

}