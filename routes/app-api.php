<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| App API Routes — 微信小程序商城接口
|--------------------------------------------------------------------------
|
| 前缀: /app-api（由 bootstrap/app.php 定义）
| 鉴权: api.auth 别名中间件（需登录的接口在对应子路由中按需使用）
|
| 按业务域拆分为独立路由文件:
|   member.php    — 认证 / 用户 / 地址 / 签到 / 积分 / 社交
|   product.php   — 商品分类 / SPU / 评价 / 收藏 / 浏览记录
|   trade.php     — 购物车 / 订单 / 售后 / 配送 / 配置 / 分销
|   pay.php       — 支付渠道 / 支付订单 / 转账 / 钱包
|   promotion.php — 优惠券 / 拼团 / 秒杀 / 积分商城 / 满减送 / 装修 / 文章 / 客服
|   system.php    — 地区 / 字典数据
|   infra.php     — 文件上传 / 租户
|   migration.php — 小程序直播 / Apple 登录
|
*/

// ==================== 会员模块 ====================
Route::prefix('member')->group(base_path('routes/member.php'));

// ==================== 商品模块 ====================
Route::prefix('product')->group(base_path('routes/product.php'));

// ==================== 交易模块 ====================
Route::prefix('trade')->group(base_path('routes/trade.php'));

// ==================== 支付模块 ====================
Route::prefix('pay')->group(base_path('routes/pay.php'));

// ==================== 营销模块 ====================
Route::prefix('promotion')->group(base_path('routes/promotion.php'));

// ==================== 系统模块 ====================
Route::prefix('system')->group(base_path('routes/system.php'));

// ==================== 基础设施 ====================
Route::prefix('infra')->group(base_path('routes/infra.php'));

// ==================== 迁移兼容 ====================
// 不设 prefix，子路由内部自行定义（/app, /third）
Route::group([], base_path('routes/migration.php'));