<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->tinyInteger('scene')->default(10)->comment('10充值/20消费/30退款/40提现');
            $table->decimal('money', 10, 2)->comment('变动金额正为增加');
            $table->string('describe', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_logs');
    }
};