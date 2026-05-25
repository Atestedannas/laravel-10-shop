<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('name', 50);
            $table->string('phone', 20);
            $table->integer('province_id')->comment('省ID');
            $table->integer('city_id')->comment('市ID');
            $table->integer('region_id')->comment('区ID');
            $table->string('detail', 255);
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};