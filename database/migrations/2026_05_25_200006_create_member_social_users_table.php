<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_social_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 30)->comment('平台类型，如 weixin / apple');
            $table->string('openid')->comment('平台唯一标识');
            $table->string('unionid')->nullable()->comment('微信 unionid');
            $table->string('nickname')->nullable()->comment('社交平台昵称');
            $table->string('avatar')->nullable()->comment('社交平台头像');
            $table->json('raw_data')->nullable()->comment('原始数据');
            $table->timestamps();

            $table->unique(['type', 'openid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_social_users');
    }
};