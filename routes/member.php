<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Member\AuthController;
use App\Http\Controllers\Api\Member\UserController;
use App\Http\Controllers\Api\Member\AddressController;
use App\Http\Controllers\Api\Member\SignInController;
use App\Http\Controllers\Api\Member\PointController;
use App\Http\Controllers\Api\Member\SocialUserController;

/*
|--------------------------------------------------------------------------
| 会员模块路由 — 前缀 /member
|--------------------------------------------------------------------------
*/

// ==================== 认证 ====================
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('sms-login', [AuthController::class, 'smsLogin']);
    Route::post('send-sms-code', [AuthController::class, 'sendSmsCode']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('social-auth-redirect', [AuthController::class, 'socialAuthRedirect']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('weixin-mini-app-login', [AuthController::class, 'weixinMiniAppLogin']);
    Route::post('create-weixin-jsapi-signature', [AuthController::class, 'createWeixinJsapiSignature']);
});

// ==================== 用户信息 ====================
Route::prefix('user')->group(function () {
    Route::get('get', [UserController::class, 'get']);
    Route::put('update', [UserController::class, 'update']);
    Route::put('update-mobile', [UserController::class, 'updateMobile']);
    Route::put('update-mobile-by-weixin', [UserController::class, 'updateMobileByWeixin']);
    Route::put('update-password', [UserController::class, 'updatePassword']);
    Route::put('reset-password', [UserController::class, 'resetPassword']);
});

// ==================== 收货地址 ====================
Route::prefix('address')->group(function () {
    Route::get('list', [AddressController::class, 'list']);
    Route::get('get', [AddressController::class, 'get']);
    Route::post('create', [AddressController::class, 'create']);
    Route::put('update', [AddressController::class, 'update']);
    Route::delete('delete', [AddressController::class, 'delete']);
});

// ==================== 签到 ====================
Route::prefix('sign-in')->group(function () {
    Route::get('config/list', [SignInController::class, 'configList']);
    Route::get('record/get-summary', [SignInController::class, 'getSummary']);
    Route::post('record/create', [SignInController::class, 'create']);
    Route::get('record/page', [SignInController::class, 'recordPage']);
});

// ==================== 积分 ====================
Route::prefix('point')->group(function () {
    Route::get('record/page', [PointController::class, 'recordPage']);
});

// ==================== 社交用户 ====================
Route::prefix('social-user')->group(function () {
    Route::get('get', [SocialUserController::class, 'get']);
    Route::post('bind', [SocialUserController::class, 'bind']);
    Route::delete('unbind', [SocialUserController::class, 'unbind']);
    Route::get('get-subscribe-template-list', [SocialUserController::class, 'getSubscribeTemplateList']);
    Route::post('wxa-qrcode', [SocialUserController::class, 'wxaQrcode']);
});