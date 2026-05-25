<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_sign_in_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('sign_date')->comment('签到日期');
            $table->integer('point')->default(0)->comment('奖励积分');
            $table->integer('day')->default(1)->comment('连续签到天数');
            $table->timestamps();

            $table->unique(['user_id', 'sign_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_sign_in_records');
    }
};