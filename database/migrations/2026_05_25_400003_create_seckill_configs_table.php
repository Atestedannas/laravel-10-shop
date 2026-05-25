<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seckill_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('时段名称，如"10点场"');
            $table->time('start_time')->comment('开始时间（HH:mm）');
            $table->time('end_time')->comment('结束时间（HH:mm）');
            $table->tinyInteger('status')->default(10)->comment('状态：10启用 20禁用');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seckill_configs');
    }
};