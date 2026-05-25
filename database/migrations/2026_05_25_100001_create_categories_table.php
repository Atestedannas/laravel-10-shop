<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('分类名称');
            $table->integer('parent_id')->default(0)->comment('父级分类ID，0为顶级')->index();
            $table->integer('sort')->default(0)->comment('排序，数字越小越靠前');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};