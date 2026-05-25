<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combination_records', function (Blueprint $table) {
            $table->id();
            $table->integer('activity_id')->comment('关联拼团活动ID')->index();
            $table->integer('user_id')->comment('团长用户ID')->index();
            $table->integer('order_id')->nullable()->comment('关联订单ID')->index();
            $table->string('group_no', 30)->unique()->comment('拼团编号');
            $table->integer('required_count')->comment('成团人数');
            $table->integer('current_count')->default(1)->comment('当前参团人数');
            $table->tinyInteger('status')->default(10)->comment('状态：10拼团中 20拼团成功 30拼团失败')->index();
            $table->dateTime('expire_time')->comment('过期时间');
            $table->dateTime('success_time')->nullable()->comment('成团时间');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combination_records');
    }
};