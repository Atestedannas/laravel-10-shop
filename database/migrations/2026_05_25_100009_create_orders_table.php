<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户ID')->index();
            $table->string('order_no', 30)->unique()->comment('订单号');
            $table->tinyInteger('order_status')->default(10)->comment('订单状态：10进行中 20已取消 21待取消 30已完成');
            $table->tinyInteger('pay_status')->default(10)->comment('支付状态：10待支付 20已支付');
            $table->tinyInteger('delivery_status')->default(10)->comment('发货状态：10未发货 20已发货 30部分发货');
            $table->tinyInteger('receipt_status')->default(10)->comment('收货状态：10未收货 20已收货');
            $table->tinyInteger('order_type')->default(10)->comment('订单类型：10实物订单 20虚拟订单');
            $table->tinyInteger('delivery_type')->default(10)->comment('配送方式：10快递配送 30无需配送');
            $table->decimal('total_price', 10, 2)->comment('订单总金额（商品原价合计）');
            $table->decimal('coupon_money', 10, 2)->default(0.00)->comment('优惠券抵扣金额');
            $table->decimal('points_money', 10, 2)->default(0.00)->comment('积分抵扣金额');
            $table->decimal('express_price', 10, 2)->default(0.00)->comment('运费');
            $table->decimal('pay_price', 10, 2)->comment('实付金额');
            $table->string('buyer_remark', 255)->nullable()->comment('买家备注');
            $table->integer('coupon_id')->nullable()->comment('使用的优惠券ID');
            $table->tinyInteger('is_use_points')->default(0)->comment('是否使用积分：0否 1是');
            $table->integer('address_id')->nullable()->comment('收货地址ID');
            $table->dateTime('create_time')->nullable()->comment('创建时间');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};