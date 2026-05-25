<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_spec_values', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_spec_id')->comment('商品规格组ID')->index();
            $table->string('spec_value', 100)->comment('规格值，如：红色、XL');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_spec_values');
    }
};