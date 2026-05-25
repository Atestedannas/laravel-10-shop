<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Product\CategoryController;
use App\Http\Controllers\Api\Product\SpuController;
use App\Http\Controllers\Api\Product\CommentController;
use App\Http\Controllers\Api\Product\FavoriteController;
use App\Http\Controllers\Api\Product\BrowseHistoryController;

/*
|--------------------------------------------------------------------------
| 商品模块路由 — 前缀 /product
|--------------------------------------------------------------------------
*/

// ==================== 商品分类 ====================
Route::prefix('category')->group(function () {
    Route::get('list', [CategoryController::class, 'list']);
    Route::get('list-by-ids', [CategoryController::class, 'listByIds']);
});

// ==================== 商品 SPU ====================
Route::prefix('spu')->group(function () {
    Route::get('page', [SpuController::class, 'page']);
    Route::get('list-by-ids', [SpuController::class, 'listByIds']);
    Route::get('get-detail', [SpuController::class, 'getDetail']);
});

// ==================== 商品评价 ====================
Route::prefix('comment')->group(function () {
    Route::get('page', [CommentController::class, 'page']);
});

// ==================== 商品收藏 ====================
Route::prefix('favorite')->group(function () {
    Route::get('page', [FavoriteController::class, 'page']);
    Route::get('exits', [FavoriteController::class, 'exits']);
    Route::post('create', [FavoriteController::class, 'create']);
    Route::delete('delete', [FavoriteController::class, 'delete']);
});

// ==================== 浏览记录 ====================
Route::prefix('browse-history')->group(function () {
    Route::get('page', [BrowseHistoryController::class, 'page']);
    Route::delete('delete', [BrowseHistoryController::class, 'delete']);
    Route::delete('clean', [BrowseHistoryController::class, 'clean']);
});