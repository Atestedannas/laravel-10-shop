<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brokerage_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique()->comment('用户ID，与users表一一对应');
            $table->integer('parent_id')->nullable()->comment('上级分销员ID')->index();
            $table->integer('level')->default(1)->comment('分销等级：1一级 2二级');
            $table->decimal('brokerage_price', 10, 2)->default(0.00)->comment('可提现佣金');
            $table->decimal('frozen_price', 10, 2)->default(0.00)->comment('冻结佣金（待结算）');
            $table->decimal('total_brokerage_price', 10, 2)->default(0.00)->comment('累计佣金总额');
            $table->decimal('total_withdraw_price', 10, 2)->default(0.00)->comment('累计提现金额');
            $table->integer('user_count')->default(0)->comment('下级人数');
            $table->integer('order_count')->default(0)->comment('推广订单数');
            $table->tinyInteger('status')->default(10)->comment('状态：10正常 20冻结');
            $table->dateTime('apply_time')->nullable()->comment('申请成为分销员时间');
            $table->dateTime('audit_time')->nullable()->comment('审核通过时间');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brokerage_users');
    }
};