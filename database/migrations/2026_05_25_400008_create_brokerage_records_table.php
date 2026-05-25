<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brokerage_records', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('获得佣金的分销员用户ID')->index();
            $table->integer('order_id')->comment('关联订单ID')->index();
            $table->integer('order_goods_id')->nullable()->comment('关联订单商品ID');
            $table->integer('source_user_id')->comment('下单用户ID');
            $table->decimal('price', 10, 2)->comment('佣金金额');
            $table->decimal('brokerage_rate', 5, 2)->default(0.00)->comment('佣金比例(%)');
            $table->tinyInteger('type')->default(10)->comment('佣金类型：10订单佣金 20下级佣金');
            $table->tinyInteger('status')->default(10)->comment('状态：10待入账 20已入账 30已退款 40已失效')->index();
            $table->dateTime('settle_time')->nullable()->comment('结算时间');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brokerage_records');
    }
};