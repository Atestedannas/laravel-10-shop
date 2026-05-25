<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('parent_id')->default(0)->index();
            $table->tinyInteger('level')->default(1)->comment('1省/2市/3区');
            $table->string('initial', 10)->nullable()->comment('首字母');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};