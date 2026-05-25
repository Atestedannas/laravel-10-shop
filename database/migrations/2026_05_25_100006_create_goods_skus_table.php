<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_skus', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id')->comment('商品ID')->index();
            $table->string('sku_spec_ids', 255)->comment('规格值ID组合，如 1_5');
            $table->decimal('goods_price', 10, 2)->comment('SKU售价');
            $table->decimal('line_price', 10, 2)->default(0.00)->comment('SKU划线价');
            $table->integer('stock')->default(0)->comment('SKU库存');
            $table->decimal('goods_weight', 8, 2)->default(0.00)->comment('商品重量（kg）');
            $table->string('goods_no', 100)->nullable()->comment('商品编码');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_skus');
    }
};