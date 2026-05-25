<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Promotion\CouponController;
use App\Http\Controllers\Api\Promotion\CouponTemplateController;
use App\Http\Controllers\Api\Promotion\CombinationActivityController;
use App\Http\Controllers\Api\Promotion\CombinationRecordController;
use App\Http\Controllers\Api\Promotion\SeckillConfigController;
use App\Http\Controllers\Api\Promotion\SeckillActivityController;
use App\Http\Controllers\Api\Promotion\PointActivityController;
use App\Http\Controllers\Api\Promotion\ActivityController;
use App\Http\Controllers\Api\Promotion\RewardActivityController;
use App\Http\Controllers\Api\Promotion\DiyTemplateController;
use App\Http\Controllers\Api\Promotion\DiyPageController;
use App\Http\Controllers\Api\Promotion\ArticleController;
use App\Http\Controllers\Api\Promotion\KefuMessageController;
use App\Http\Controllers\Api\Promotion\BargainActivityController;
use App\Http\Controllers\Api\Promotion\BargainRecordController;

/*
|--------------------------------------------------------------------------
| 营销模块路由 — 前缀 /promotion
|--------------------------------------------------------------------------
*/

// ==================== 优惠券模板 ====================
Route::prefix('coupon-template')->group(function () {
    Route::get('list-by-ids', [CouponTemplateController::class, 'listByIds']);
    Route::get('list', [CouponTemplateController::class, 'list']);
    Route::get('page', [CouponTemplateController::class, 'page']);
    Route::get('get', [CouponTemplateController::class, 'get']);
});

// ==================== 优惠券 ====================
Route::prefix('coupon')->group(function () {
    Route::get('page', [CouponController::class, 'page']);
    Route::post('take', [CouponController::class, 'take']);
    Route::get('get', [CouponController::class, 'get']);
    Route::get('get-unused-count', [CouponController::class, 'getUnusedCount']);
});

// ==================== 拼团活动 ====================
Route::prefix('combination-activity')->group(function () {
    Route::get('page', [CombinationActivityController::class, 'page']);
    Route::get('get-detail', [CombinationActivityController::class, 'getDetail']);
    Route::get('list-by-ids', [CombinationActivityController::class, 'listByIds']);
});

// ==================== 拼团记录 ====================
Route::prefix('combination-record')->group(function () {
    Route::get('get-head-list', [CombinationRecordController::class, 'getHeadList']);
    Route::get('page', [CombinationRecordController::class, 'page']);
    Route::get('get-detail', [CombinationRecordController::class, 'getDetail']);
    Route::get('get-summary', [CombinationRecordController::class, 'getSummary']);
});

// ==================== 秒杀配置 ====================
Route::prefix('seckill-config')->group(function () {
    Route::get('list', [SeckillConfigController::class, 'list']);
});

// ==================== 秒杀活动 ====================
Route::prefix('seckill-activity')->group(function () {
    Route::get('get-now', [SeckillActivityController::class, 'getNow']);
    Route::get('page', [SeckillActivityController::class, 'page']);
    Route::get('list-by-ids', [SeckillActivityController::class, 'listByIds']);
    Route::get('get-detail', [SeckillActivityController::class, 'getDetail']);
});

// ==================== 积分商城 ====================
Route::prefix('point-activity')->group(function () {
    Route::get('page', [PointActivityController::class, 'page']);
    Route::get('list-by-ids', [PointActivityController::class, 'listByIds']);
    Route::get('get-detail', [PointActivityController::class, 'getDetail']);
});

// ==================== 活动聚合 ====================
Route::prefix('activity')->group(function () {
    Route::get('list-by-spu-id', [ActivityController::class, 'listBySpuId']);
});

// ==================== 满减送 ====================
Route::prefix('reward-activity')->group(function () {
    Route::get('get', [RewardActivityController::class, 'get']);
});

// ==================== 页面装修 ====================
Route::prefix('diy-template')->group(function () {
    Route::get('used', [DiyTemplateController::class, 'used']);
    Route::get('get', [DiyTemplateController::class, 'get']);
});

Route::prefix('diy-page')->group(function () {
    Route::get('get', [DiyPageController::class, 'get']);
});

// ==================== 文章 ====================
Route::prefix('article')->group(function () {
    Route::get('get', [ArticleController::class, 'get']);
});

// ==================== 客服消息 ====================
Route::prefix('kefu-message')->group(function () {
    Route::post('send', [KefuMessageController::class, 'send']);
    Route::get('list', [KefuMessageController::class, 'list']);
});

// ==================== 砍价活动 ====================
Route::prefix('bargain-activity')->group(function () {
    Route::get('page', [BargainActivityController::class, 'page']);
    Route::get('get-detail', [BargainActivityController::class, 'getDetail']);
    Route::get('my-bargains', [BargainActivityController::class, 'myBargains']);
});

// ==================== 砍价记录 ====================
Route::prefix('bargain-record')->group(function () {
    Route::get('page', [BargainRecordController::class, 'page']);
    Route::post('help', [BargainRecordController::class, 'help']);
    Route::get('get-progress', [BargainRecordController::class, 'getProgress']);
});