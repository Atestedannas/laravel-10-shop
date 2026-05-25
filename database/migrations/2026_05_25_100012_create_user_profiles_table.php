<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique()->comment('用户ID（与users表关联）');
            $table->string('phone', 20)->nullable()->comment('手机号');
            $table->string('avatar', 255)->nullable()->comment('头像URL');
            $table->string('nickname', 50)->nullable()->comment('昵称');
            $table->decimal('balance', 10, 2)->default(0.00)->comment('用户余额');
            $table->integer('points')->default(0)->comment('用户积分');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};