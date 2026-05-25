<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('活动名称');
            $table->tinyInteger('type')->default(10)->comment('活动类型：10满减 20满折 30满赠');
            $table->decimal('threshold_price', 10, 2)->comment('满足金额（元）');
            $table->decimal('discount_price', 10, 2)->default(0.00)->comment('减/折金额，满折时为折扣率(如9折=90.00)');
            $table->integer('gift_goods_id')->nullable()->comment('赠品商品ID')->index();
            $table->integer('gift_goods_sku_id')->nullable()->comment('赠品规格SKU ID');
            $table->integer('gift_count')->nullable()->comment('赠品数量');
            $table->tinyInteger('scope_type')->default(10)->comment('适用范围：10全部商品 20指定分类 30指定商品');
            $table->json('scope_value')->nullable()->comment('适用范围值(分类ID列表或商品ID列表)');
            $table->dateTime('start_time')->comment('活动开始时间');
            $table->dateTime('end_time')->comment('活动结束时间');
            $table->tinyInteger('status')->default(10)->comment('状态：10进行中 20已结束 30未开始')->index();
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes()->comment('软删除');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_activities');
    }
};