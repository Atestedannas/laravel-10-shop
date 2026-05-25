<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->tinyInteger('coupon_type')->default(10)->comment('10满减/20折扣');
            $table->decimal('reduce_price', 10, 2)->default(0)->comment('满减金额');
            $table->decimal('discount', 3, 2)->default(0)->comment('折扣如0.90');
            $table->decimal('min_price', 10, 2)->default(0)->comment('最低消费');
            $table->tinyInteger('expire_type')->default(10)->comment('10领取后N天/20固定时间');
            $table->integer('expire_day')->default(0)->comment('领取后有效天数');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('describe', 500)->nullable();
            $table->tinyInteger('apply_range')->default(10)->comment('10全部/20指定');
            $table->integer('total_num')->default(-1)->comment('-1不限量');
            $table->integer('receive_num')->default(0);
            $table->integer('sort')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};