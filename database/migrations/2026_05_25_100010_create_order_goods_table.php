<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->comment('订单ID')->index();
            $table->integer('goods_id')->comment('商品ID')->index();
            $table->integer('goods_sku_id')->comment('商品SKU ID');
            $table->string('goods_name', 255)->comment('商品名称快照');
            $table->string('goods_image', 255)->comment('商品图片快照');
            $table->decimal('goods_price', 10, 2)->comment('商品成交价');
            $table->integer('total_num')->comment('购买数量');
            $table->string('goods_props', 255)->nullable()->comment('规格属性JSON，如 [{"name":"颜色","value":"红色"}]');
            $table->tinyInteger('is_user_grade')->default(0)->comment('是否会员折扣：0否 1是');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_goods');
    }
};