<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('goods_id')->index();
            $table->integer('order_id')->index();
            $table->integer('order_goods_id')->index();
            $table->tinyInteger('score')->default(5);
            $table->string('content', 500)->nullable();
            $table->string('images', 1000)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1显示/0隐藏');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};