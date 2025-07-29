<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->enum('type', ['customer_payment', 'vendor_payment', 'employee_payment', 'other']);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->unsignedBigInteger('payroll_id')->nullable();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->enum('method', ['cash', 'check', 'credit_card', 'bank_transfer', 'paypal', 'stripe', 'other']);
            $table->string('reference_number')->nullable();
            $table->string('check_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('deposit_account_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('accounting_customers')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('accounting_vendors')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('accounting_employees')->onDelete('set null');
            $table->foreign('invoice_id')->references('id')->on('accounting_invoices')->onDelete('set null');
            $table->foreign('expense_id')->references('id')->on('accounting_expenses')->onDelete('set null');
            $table->foreign('payroll_id')->references('id')->on('accounting_payroll')->onDelete('set null');
            $table->foreign('deposit_account_id')->references('id')->on('accounting_accounts')->onDelete('set null');
            $table->index(['type', 'status']);
            $table->index('payment_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_payments');
    }
};
