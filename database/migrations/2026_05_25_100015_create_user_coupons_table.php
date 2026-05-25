<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('coupon_id')->index();
            $table->tinyInteger('is_used')->default(0);
            $table->tinyInteger('is_expired')->default(0);
            $table->dateTime('used_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
    }
};