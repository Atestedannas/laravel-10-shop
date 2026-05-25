<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_address', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->comment('订单ID')->index();
            $table->string('name', 50)->comment('收货人姓名');
            $table->string('phone', 20)->comment('联系电话');
            $table->string('province', 50)->comment('省份');
            $table->string('city', 50)->comment('城市');
            $table->string('region', 50)->comment('区县');
            $table->string('detail', 255)->comment('详细地址');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_address');
    }
};