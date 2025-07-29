<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number')->unique();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');
            $table->string('tax_number')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_terms', ['net_15', 'net_30', 'net_45', 'net_60', 'due_on_receipt'])->default('net_30');
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('customer_number');
            $table->index('email');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_customers');
    }
};
