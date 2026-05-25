<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brokerage_withdraws', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('分销员用户ID')->index();
            $table->string('order_no', 30)->unique()->comment('提现单号');
            $table->decimal('price', 10, 2)->comment('提现金额');
            $table->decimal('service_fee', 10, 2)->default(0.00)->comment('手续费');
            $table->decimal('real_price', 10, 2)->comment('实际到账金额');
            $table->tinyInteger('type')->default(10)->comment('提现方式：10微信 20支付宝 30银行卡');
            $table->string('bank_name', 100)->nullable()->comment('银行名称');
            $table->string('bank_account', 50)->nullable()->comment('银行账号');
            $table->string('bank_user', 50)->nullable()->comment('开户人姓名');
            $table->tinyInteger('status')->default(10)->comment('状态：10审核中 20审核通过 30审核拒绝 40已打款')->index();
            $table->string('refuse_reason', 255)->nullable()->comment('拒绝原因');
            $table->dateTime('audit_time')->nullable()->comment('审核时间');
            $table->dateTime('transfer_time')->nullable()->comment('打款时间');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brokerage_withdraws');
    }
};