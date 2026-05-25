<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recharges', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('order_no', 30)->unique();
            $table->integer('plan_id')->nullable()->comment('充值套餐ID');
            $table->decimal('money', 10, 2);
            $table->tinyInteger('pay_status')->default(10)->comment('10待支付/20已支付');
            $table->string('pay_method', 30)->nullable();
            $table->dateTime('pay_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recharges');
    }
};