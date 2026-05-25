<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_specs', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id')->comment('商品ID')->index();
            $table->string('spec_name', 100)->comment('规格组名称，如：颜色、尺寸');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_specs');
    }
};