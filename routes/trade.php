<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Trade\CartController;
use App\Http\Controllers\Api\Trade\OrderController;
use App\Http\Controllers\Api\Trade\AfterSaleController;
use App\Http\Controllers\Api\Trade\DeliveryController;
use App\Http\Controllers\Api\Trade\ConfigController;
use App\Http\Controllers\Api\Promotion\BrokerageUserController;
use App\Http\Controllers\Api\Promotion\BrokerageRecordController;
use App\Http\Controllers\Api\Promotion\BrokerageWithdrawController;

/*
|--------------------------------------------------------------------------
| 交易模块路由 — 前缀 /trade
|--------------------------------------------------------------------------
*/

// ==================== 购物车 ====================
Route::prefix('cart')->group(function () {
    Route::get('list', [CartController::class, 'list']);
    Route::post('add', [CartController::class, 'add']);
    Route::put('update-count', [CartController::class, 'updateCount']);
    Route::put('update-selected', [CartController::class, 'updateSelected']);
    Route::delete('delete', [CartController::class, 'delete']);
});

// ==================== 订单 ====================
Route::prefix('order')->group(function () {
    Route::get('settlement', [OrderController::class, 'settlement']);
    Route::get('settlement-product', [OrderController::class, 'settlementProduct']);
    Route::post('create', [OrderController::class, 'create']);
    Route::get('get-detail', [OrderController::class, 'getDetail']);
    Route::get('page', [OrderController::class, 'page']);
    Route::put('receive', [OrderController::class, 'receive']);
    Route::delete('cancel', [OrderController::class, 'cancel']);
    Route::delete('delete', [OrderController::class, 'delete']);
    Route::get('get-express-track-list', [OrderController::class, 'getExpressTrackList']);
    Route::get('get-count', [OrderController::class, 'getCount']);
    Route::prefix('item')->group(function () {
        Route::post('create-comment', [OrderController::class, 'createComment']);
    });
});

// ==================== 售后 ====================
Route::prefix('after-sale')->group(function () {
    Route::get('page', [AfterSaleController::class, 'page']);
    Route::post('create', [AfterSaleController::class, 'create']);
    Route::get('get', [AfterSaleController::class, 'get']);
    Route::delete('cancel', [AfterSaleController::class, 'cancel']);
    Route::put('delivery', [AfterSaleController::class, 'delivery']);
});

// ==================== 售后日志 ====================
Route::prefix('after-sale-log')->group(function () {
    Route::get('list', [AfterSaleController::class, 'logList']);
});

// ==================== 配送 ====================
Route::prefix('delivery')->group(function () {
    Route::prefix('express')->group(function () {
        Route::get('list', [DeliveryController::class, 'expressList']);
    });
    Route::prefix('pick-up-store')->group(function () {
        Route::get('list', [DeliveryController::class, 'pickUpStoreList']);
        Route::get('get', [DeliveryController::class, 'pickUpStoreGet']);
    });
});

// ==================== 交易配置 ====================
Route::prefix('config')->group(function () {
    Route::get('get', [ConfigController::class, 'get']);
});

// ==================== 分销 ====================
Route::prefix('brokerage-user')->group(function () {
    Route::put('bind', [BrokerageUserController::class, 'bind']);
    Route::get('get', [BrokerageUserController::class, 'get']);
    Route::get('get-summary', [BrokerageUserController::class, 'getSummary']);
    Route::get('get-rank-by-price', [BrokerageUserController::class, 'getRankByPrice']);
    Route::get('rank-page-by-price', [BrokerageUserController::class, 'rankPageByPrice']);
    Route::get('rank-page-by-user-count', [BrokerageUserController::class, 'rankPageByUserCount']);
    Route::get('child-summary-page', [BrokerageUserController::class, 'childSummaryPage']);
});

Route::prefix('brokerage-record')->group(function () {
    Route::get('page', [BrokerageRecordController::class, 'page']);
    Route::get('get-product-brokerage-price', [BrokerageRecordController::class, 'getProductBrokeragePrice']);
});

Route::prefix('brokerage-withdraw')->group(function () {
    Route::post('create', [BrokerageWithdrawController::class, 'create']);
    Route::get('page', [BrokerageWithdrawController::class, 'page']);
    Route::get('get', [BrokerageWithdrawController::class, 'get']);
});