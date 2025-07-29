<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->date('expense_date');
            $table->enum('status', ['draft', 'pending', 'approved', 'paid', 'rejected'])->default('draft');
            $table->string('category');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->enum('payment_method', ['cash', 'check', 'credit_card', 'bank_transfer', 'other'])->nullable();
            $table->string('reference_number')->nullable();
            $table->string('receipt_number')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->boolean('is_reimbursable')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('accounting_vendors')->onDelete('set null');
            $table->foreign('account_id')->references('id')->on('accounting_accounts')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('accounting_customers')->onDelete('set null');
            $table->index(['status', 'expense_date']);
            $table->index('expense_date');
            $table->index('category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_expenses');
    }
};
