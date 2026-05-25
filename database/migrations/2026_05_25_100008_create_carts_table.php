<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户ID')->index();
            $table->integer('goods_id')->comment('商品ID')->index();
            $table->integer('goods_sku_id')->comment('商品SKU ID')->index();
            $table->integer('goods_num')->default(1)->comment('购买数量');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};