<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_sign_in_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('day')->comment('连续签到天数');
            $table->integer('point')->comment('奖励积分');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_sign_in_configs');
    }
};