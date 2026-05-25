<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Pay\ChannelController;
use App\Http\Controllers\Api\Pay\OrderController;
use App\Http\Controllers\Api\Pay\TransferController;
use App\Http\Controllers\Api\Pay\WalletController;
use App\Http\Controllers\Api\Pay\WalletTransactionController;
use App\Http\Controllers\Api\Pay\WalletRechargeController;
use App\Http\Controllers\Api\Pay\WalletRechargePackageController;

/*
|--------------------------------------------------------------------------
| 支付模块路由 — 前缀 /pay
|--------------------------------------------------------------------------
*/

// ==================== 支付渠道 ====================
Route::prefix('channel')->group(function () {
    Route::get('get-enable-code-list', [ChannelController::class, 'getEnableCodeList']);
});

// ==================== 支付订单 ====================
Route::prefix('order')->group(function () {
    Route::get('get', [OrderController::class, 'get']);
    Route::post('submit', [OrderController::class, 'submit']);
});

// ==================== 转账 ====================
Route::prefix('transfer')->group(function () {
    Route::get('sync', [TransferController::class, 'sync']);
});

// ==================== 钱包 ====================
Route::prefix('wallet')->group(function () {
    Route::get('get', [WalletController::class, 'get']);
});

// ==================== 钱包流水 ====================
Route::prefix('wallet-transaction')->group(function () {
    Route::get('page', [WalletTransactionController::class, 'page']);
    Route::get('get-summary', [WalletTransactionController::class, 'getSummary']);
});

// ==================== 充值套餐 ====================
Route::prefix('wallet-recharge-package')->group(function () {
    Route::get('list', [WalletRechargePackageController::class, 'list']);
});

// ==================== 充值 ====================
Route::prefix('wallet-recharge')->group(function () {
    Route::post('create', [WalletRechargeController::class, 'create']);
    Route::get('page', [WalletRechargeController::class, 'page']);
});