<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\System\AreaController;
use App\Http\Controllers\Api\System\DictDataController;
use App\Http\Controllers\Api\System\TenantController;

/*
|--------------------------------------------------------------------------
| 系统模块路由 — 前缀 /system
|--------------------------------------------------------------------------
*/

// ==================== 地区 ====================
Route::prefix('area')->group(function () {
    Route::get('tree', [AreaController::class, 'tree']);
});

// ==================== 字典数据 ====================
Route::prefix('dict-data')->group(function () {
    Route::get('type', [DictDataController::class, 'type']);
});

// ==================== 租户 ====================
Route::prefix('tenant')->group(function () {
    Route::get('get-by-website', [TenantController::class, 'getByWebsite']);
});