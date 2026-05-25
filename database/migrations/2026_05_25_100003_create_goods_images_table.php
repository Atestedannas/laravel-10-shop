<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_images', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id')->comment('商品ID')->index();
            $table->string('image_url', 255)->comment('图片URL');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_images');
    }
};