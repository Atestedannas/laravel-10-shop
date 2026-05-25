<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->comment('商品分类ID')->index();
            $table->string('goods_name', 255)->comment('商品名称');
            $table->string('goods_image', 255)->nullable()->comment('商品主图');
            $table->string('selling_point', 255)->nullable()->comment('商品卖点');
            $table->tinyInteger('spec_type')->default(10)->comment('规格类型：10单规格 20多规格');
            $table->text('content')->nullable()->comment('商品描述（HTML）');
            $table->decimal('goods_price_min', 10, 2)->default(0.00)->comment('商品最低价');
            $table->decimal('line_price_min', 10, 2)->default(0.00)->comment('划线价最低');
            $table->integer('goods_sales')->default(0)->comment('商品销量');
            $table->tinyInteger('goods_status')->default(10)->comment('商品状态：10上架 20下架')->index();
            $table->integer('sort')->default(0)->comment('排序，数字越小越靠前');
            $table->string('video', 255)->nullable()->comment('商品视频URL');
            $table->string('video_cover', 255)->nullable()->comment('视频封面图URL');
            $table->tinyInteger('is_user_grade')->default(0)->comment('是否开启会员折扣：0关闭 1开启');
            $table->timestamps();
            $table->softDeletes()->comment('软删除');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};