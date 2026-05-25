<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 20)->nullable()->unique()->after('email');
            $table->string('nickname', 50)->nullable()->after('name');
            $table->string('avatar')->nullable()->after('nickname');
            $table->integer('point')->default(0)->after('is_admin');
            $table->tinyInteger('sex')->default(0)->comment('0未知 1男 2女')->after('point');
            $table->date('birthday')->nullable()->after('sex');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'nickname', 'avatar', 'point', 'sex', 'birthday']);
        });
    }
};