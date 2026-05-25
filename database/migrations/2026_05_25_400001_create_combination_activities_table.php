<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combination_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id')->comment('关联商品ID')->index();
            $table->integer('goods_sku_id')->nullable()->comment('关联规格SKU ID，为空则适用所有规格')->index();
            $table->decimal('group_price', 10, 2)->comment('拼团价');
            $table->integer('required_count')->default(2)->comment('成团人数');
            $table->integer('limit_time')->default(1440)->comment('拼团时限（分钟），默认24小时');
            $table->integer('stock')->default(0)->comment('拼团库存');
            $table->integer('virtual_sales')->default(0)->comment('虚拟销量');
            $table->integer('limit_count')->nullable()->comment('每人限购数量，null不限购');
            $table->dateTime('start_time')->comment('活动开始时间');
            $table->dateTime('end_time')->comment('活动结束时间');
            $table->tinyInteger('status')->default(10)->comment('状态：10进行中 20已结束 30未开始')->index();
            $table->integer('sort')->default(0)->comment('排序，数字越小越靠前');
            $table->timestamps();
            $table->softDeletes()->comment('软删除');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combination_activities');
    }
};