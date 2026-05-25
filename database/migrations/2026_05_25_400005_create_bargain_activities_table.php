<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bargain_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id')->comment('关联商品ID')->index();
            $table->integer('goods_sku_id')->nullable()->comment('关联规格SKU ID')->index();
            $table->decimal('origin_price', 10, 2)->comment('商品原价');
            $table->decimal('min_price', 10, 2)->comment('最低可砍至价格');
            $table->decimal('bargain_min', 10, 2)->default(0.01)->comment('单次最低砍价金额');
            $table->decimal('bargain_max', 10, 2)->default(0.00)->comment('单次最高砍价金额');
            $table->integer('stock')->default(0)->comment('活动库存');
            $table->integer('help_max')->default(0)->comment('一个用户最多帮砍次数，0不限制');
            $table->integer('self_bargain_max')->default(1)->comment('自己最多可砍次数');
            $table->tinyInteger('bargain_mode')->default(10)->comment('砍价模式：10随机金额 20固定金额 30递增金额');
            $table->integer('virtual_sales')->default(0)->comment('虚拟销量');
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
        Schema::dropIfExists('bargain_activities');
    }
};