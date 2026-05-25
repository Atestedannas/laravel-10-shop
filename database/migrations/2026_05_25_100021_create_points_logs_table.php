<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->tinyInteger('scene')->default(10)->comment('10签到/20购物/30消费抵扣');
            $table->integer('points')->comment('变动积分正为增加');
            $table->string('describe', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_logs');
    }
};