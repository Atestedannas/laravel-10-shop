<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 50)->comment('收货人姓名');
            $table->string('mobile', 20)->comment('收货人手机号');
            $table->integer('province_id')->default(0);
            $table->integer('city_id')->default(0);
            $table->integer('district_id')->default(0);
            $table->string('province', 50)->comment('省份');
            $table->string('city', 50)->comment('城市');
            $table->string('district', 50)->comment('区/县');
            $table->string('detail')->comment('详细地址');
            $table->tinyInteger('is_default')->default(0)->comment('是否默认地址');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_addresses');
    }
};