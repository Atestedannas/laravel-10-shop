<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_services', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id')->comment('商品ID')->index();
            $table->string('service_name', 100)->comment('服务名称，如：7天无理由');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_services');
    }
};