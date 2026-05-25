<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seckill_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('config_id')->comment('关联秒杀时段配置ID')->index();
            $table->integer('goods_id')->comment('关联商品ID')->index();
            $table->integer('goods_sku_id')->nullable()->comment('关联规格SKU ID')->index();
            $table->decimal('seckill_price', 10, 2)->comment('秒杀价');
            $table->integer('seckill_stock')->default(0)->comment('秒杀库存');
            $table->integer('sold_count')->default(0)->comment('已售数量');
            $table->integer('limit_count')->nullable()->comment('每人限购数量，null不限购');
            $table->decimal('origin_price', 10, 2)->comment('原价');
            $table->date('start_date')->comment('活动日期');
            $table->tinyInteger('status')->default(10)->comment('状态：10进行中 20已结束 30未开始')->index();
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes()->comment('软删除');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seckill_activities');
    }
};