<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GoodsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderCommentController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\MyCouponController;
use App\Http\Controllers\Api\UserCouponController;
use App\Http\Controllers\Api\BalanceLogController;
use App\Http\Controllers\Api\PointsLogController;
use App\Http\Controllers\Api\RechargeController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\CaptchaController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ExpressController;
use App\Http\Controllers\Api\HelpController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {

    // ============================================================
    // RBAC 路由（保留原有）
    // ============================================================

    // 角色管理
    Route::apiResource('roles', RoleController::class);
    // 权限管理
    Route::apiResource('permissions', PermissionController::class);

    // 用户角色分配（需要指定用户ID）
    Route::prefix('users')->group(function () {
        Route::get('/{userId}/roles', [UserRoleController::class, 'userRolesList']);
        Route::post('/{userId}/roles', [UserRoleController::class, 'assignRolesToUsers']);
        Route::get('/{userId}/permissions', [UserRoleController::class, 'userPermissionsList']);
        Route::delete('/{userId}/roles/{roleId}', [UserRoleController::class, 'cancleUserRole']);
    });

    Route::post('/roles/{role}/permissions', [RoleController::class, 'grantPermissions']);

    // 当前登录用户（不需要ID）
    Route::prefix('user')->group(function () {
        Route::get('/roles', [UserRoleController::class, 'currentUserRoles']);
        Route::get('/permissions', [UserRoleController::class, 'currentUserPermissions']);
    });

    // ============================================================
    // 商城 API 路由
    // ============================================================

    // --- 登录模块 (passport) ---
    Route::prefix('passport')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('loginMpWx', [AuthController::class, 'loginMpWx']);
        Route::post('loginMpWxMobile', [AuthController::class, 'loginMpWxMobile']);
        Route::post('isPersonalMpweixin', [AuthController::class, 'isPersonalMpweixin']);
    });

    // --- 用户模块 (user) ---
    Route::prefix('user')->group(function () {
        Route::get('info', [UserController::class, 'info'])->middleware('auth:sanctum');
        Route::get('assets', [UserController::class, 'assets'])->middleware('auth:sanctum');
        Route::post('bindMobile', [UserController::class, 'bindMobile'])->middleware('auth:sanctum');
        Route::post('personal', [UserController::class, 'personal'])->middleware('auth:sanctum');
    });

    // --- 商品模块 (goods) ---
    Route::prefix('goods')->group(function () {
        Route::get('list', [GoodsController::class, 'list']);
        Route::get('detail', [GoodsController::class, 'detail']);
        Route::get('basic', [GoodsController::class, 'basic']);
        Route::get('specData', [GoodsController::class, 'specData']);
        Route::get('skuInfo', [GoodsController::class, 'skuInfo']);
        Route::get('service/list', [GoodsController::class, 'serviceList']);
    });

    // --- 分类模块 (category) ---
    Route::prefix('category')->group(function () {
        Route::get('list', [CategoryController::class, 'list']);
    });

    // --- 购物车模块 (cart) ---
    Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [CartController::class, 'list']);
        Route::get('total', [CartController::class, 'total']);
        Route::post('add', [CartController::class, 'add']);
        Route::post('update', [CartController::class, 'update']);
        Route::post('clear', [CartController::class, 'clear']);
    });

    // --- 结算模块 (checkout) ---
    Route::prefix('checkout')->middleware('auth:sanctum')->group(function () {
        Route::get('order', [CheckoutController::class, 'order']);
        Route::post('submit', [CheckoutController::class, 'submit']);
    });

    // --- 收银台模块 (cashier) ---
    Route::prefix('cashier')->middleware('auth:sanctum')->group(function () {
        Route::get('orderInfo', [CashierController::class, 'orderInfo']);
        Route::post('orderPay', [CashierController::class, 'orderPay']);
        Route::get('tradeQuery', [CashierController::class, 'tradeQuery']);
    });

    // --- 订单模块 (order) ---
    Route::prefix('order')->middleware('auth:sanctum')->group(function () {
        Route::get('todoCounts', [OrderController::class, 'todoCounts']);
        Route::get('list', [OrderController::class, 'list']);
        Route::get('detail', [OrderController::class, 'detail']);
        Route::get('express', [OrderController::class, 'express']);
        Route::post('cancel', [OrderController::class, 'cancel']);
        Route::post('receipt', [OrderController::class, 'receipt']);
    });

    // --- 订单评价模块 (order.comment) ---
    Route::prefix('order.comment')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [OrderCommentController::class, 'list']);
        Route::post('submit', [OrderCommentController::class, 'submit']);
    });

    // --- 商品评价模块 (comment) ---
    Route::prefix('comment')->group(function () {
        Route::get('list', [CommentController::class, 'list']);
        Route::get('listRows', [CommentController::class, 'listRows']);
        Route::get('total', [CommentController::class, 'total']);
    });

    // --- 售后模块 (refund) ---
    Route::prefix('refund')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [RefundController::class, 'list']);
        Route::get('goods', [RefundController::class, 'goods']);
        Route::post('apply', [RefundController::class, 'apply']);
        Route::get('detail', [RefundController::class, 'detail']);
        Route::post('delivery', [RefundController::class, 'delivery']);
    });

    // --- 收货地址模块 (address) ---
    Route::prefix('address')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [AddressController::class, 'list']);
        Route::get('defaultId', [AddressController::class, 'defaultId']);
        Route::get('detail', [AddressController::class, 'detail']);
        Route::post('add', [AddressController::class, 'add']);
        Route::post('edit', [AddressController::class, 'edit']);
        Route::post('setDefault', [AddressController::class, 'setDefault']);
        Route::post('remove', [AddressController::class, 'remove']);
    });

    // --- 优惠券模块 (coupon) ---
    Route::prefix('coupon')->group(function () {
        Route::get('list', [CouponController::class, 'list']);
    });

    // --- 我的优惠券模块 (myCoupon) ---
    Route::prefix('myCoupon')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [MyCouponController::class, 'list']);
        Route::post('receive', [MyCouponController::class, 'receive']);
    });

    // --- 用户优惠券模块 (user.coupon) ---
    Route::prefix('user.coupon')->middleware('auth:sanctum')->group(function () {
        Route::post('receive', [UserCouponController::class, 'receive']);
    });

    // --- 余额日志模块 (balance.log) ---
    Route::prefix('balance.log')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [BalanceLogController::class, 'list']);
    });

    // --- 积分日志模块 (points.log) ---
    Route::prefix('points.log')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [PointsLogController::class, 'list']);
    });

    // --- 充值模块 (recharge) ---
    Route::prefix('recharge')->middleware('auth:sanctum')->group(function () {
        Route::get('center', [RechargeController::class, 'center']);
        Route::post('submit', [RechargeController::class, 'submit']);
        Route::get('tradeQuery', [RechargeController::class, 'tradeQuery']);
    });

    // --- 充值订单模块 (recharge.order) ---
    Route::prefix('recharge.order')->middleware('auth:sanctum')->group(function () {
        Route::get('list', [RechargeController::class, 'orderList']);
    });

    // --- 文章模块 (article) ---
    Route::prefix('article')->group(function () {
        Route::get('category/list', [ArticleController::class, 'categoryList']);
        Route::get('list', [ArticleController::class, 'list']);
        Route::get('detail', [ArticleController::class, 'detail']);
    });

    // --- 自定义页面模块 (page) ---
    Route::prefix('page')->group(function () {
        Route::get('detail', [PageController::class, 'detail']);
    });

    // --- 地区模块 (region) ---
    Route::prefix('region')->group(function () {
        Route::get('all', [RegionController::class, 'all']);
        Route::get('tree', [RegionController::class, 'tree']);
    });

    // --- 商城设置模块 (setting) ---
    Route::prefix('setting')->group(function () {
        Route::get('data', [SettingController::class, 'data']);
    });

    // --- 商城基础信息 (store) ---
    Route::prefix('store')->group(function () {
        Route::get('data', [StoreController::class, 'data']);
    });

    // --- 验证码 (captcha) ---
    Route::prefix('captcha')->group(function () {
        Route::get('image', [CaptchaController::class, 'image']);
        Route::post('sendSmsCaptcha', [CaptchaController::class, 'sendSmsCaptcha']);
    });

    // --- 文件上传 (upload) ---
    Route::prefix('upload')->middleware('auth:sanctum')->group(function () {
        Route::post('image', [UploadController::class, 'image']);
    });

    // --- 物流 (express) ---
    Route::prefix('express')->group(function () {
        Route::get('list', [ExpressController::class, 'list']);
    });

    // --- 帮助中心 (help) ---
    Route::prefix('help')->group(function () {
        Route::get('list', [HelpController::class, 'list']);
    });

});