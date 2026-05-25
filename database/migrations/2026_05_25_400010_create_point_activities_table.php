<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id')->comment('关联商品ID')->index();
            $table->integer('goods_sku_id')->nullable()->comment('关联规格SKU ID')->index();
            $table->integer('point_price')->comment('积分价格');
            $table->integer('stock')->default(0)->comment('兑换库存');
            $table->integer('sold_count')->default(0)->comment('已兑换数量');
            $table->integer('limit_count')->nullable()->comment('每人限兑数量，null不限');
            $table->decimal('origin_price', 10, 2)->comment('商品原价');
            $table->string('image', 255)->nullable()->comment('积分商品封面图');
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
        Schema::dropIfExists('point_activities');
    }
};