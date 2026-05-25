<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Migration\AppController;
use App\Http\Controllers\Api\Migration\ThirdController;

/*
|--------------------------------------------------------------------------
| 迁移兼容模块路由
|--------------------------------------------------------------------------
*/

// ==================== 小程序直播（原有 /app 路径） ====================
Route::prefix('app')->group(function () {
    Route::prefix('mplive')->group(function () {
        Route::get('getRoomList', [AppController::class, 'getRoomList']);
        Route::get('getMpLink', [AppController::class, 'getMpLink']);
    });
});

// ==================== 第三方登录 ====================
Route::prefix('third')->group(function () {
    Route::prefix('apple')->group(function () {
        Route::post('login', [ThirdController::class, 'appleLogin']);
    });
});