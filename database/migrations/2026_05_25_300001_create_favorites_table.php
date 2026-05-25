<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('spu_id')->comment('商品ID');
            $table->timestamps();

            $table->index(['user_id', 'spu_id']);
            $table->unique(['user_id', 'spu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};