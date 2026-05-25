<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bargain_records', function (Blueprint $table) {
            $table->id();
            $table->integer('activity_id')->comment('关联砍价活动ID')->index();
            $table->integer('user_id')->comment('发起砍价的用户ID')->index();
            $table->integer('order_id')->nullable()->comment('砍价成功后关联的订单ID')->index();
            $table->decimal('origin_price', 10, 2)->comment('商品原价');
            $table->decimal('current_price', 10, 2)->comment('当前价格');
            $table->decimal('bargain_total', 10, 2)->default(0.00)->comment('累计已砍金额');
            $table->integer('help_count')->default(0)->comment('帮砍人次');
            $table->tinyInteger('status')->default(10)->comment('状态：10砍价中 20砍价成功 30砍价失败 40已下单')->index();
            $table->dateTime('expire_time')->comment('过期时间');
            $table->dateTime('success_time')->nullable()->comment('砍价成功时间');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bargain_records');
    }
};