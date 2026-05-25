<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Infra\FileController;

/*
|--------------------------------------------------------------------------
| 基础设施模块路由 — 前缀 /infra
|--------------------------------------------------------------------------
*/

// ==================== 文件 ====================
Route::prefix('file')->group(function () {
    Route::post('upload', [FileController::class, 'upload']);
    Route::get('presigned-url', [FileController::class, 'presignedUrl']);
    Route::post('create', [FileController::class, 'create']);
});