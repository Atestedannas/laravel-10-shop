<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('order_id')->index();
            $table->integer('order_goods_id')->index();
            $table->tinyInteger('refund_type')->default(10)->comment('10退货退款/20换货');
            $table->tinyInteger('refund_status')->default(0)->comment('0进行中/10已拒绝/20已完成/30已取消');
            $table->decimal('amount', 10, 2);
            $table->string('content', 500);
            $table->string('images', 1000)->nullable()->comment('图片ID逗号分隔');
            $table->tinyInteger('audit_status')->default(0)->comment('0待审核/10已同意/20已拒绝');
            $table->string('express_no', 100)->nullable()->comment('退货物流单号');
            $table->string('express_company', 100)->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};