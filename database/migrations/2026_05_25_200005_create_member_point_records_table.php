<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_point_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->comment('积分标题/说明');
            $table->integer('point')->comment('积分变化值，正数为增加，负数为消耗');
            $table->integer('total_point')->comment('变化后总积分');
            $table->tinyInteger('add_status')->comment('1增加 2消耗');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_point_records');
    }
};