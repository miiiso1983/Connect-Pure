<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_recurring_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_name');
            $table->enum('type', ['invoice', 'expense', 'payment']);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->enum('frequency', ['weekly', 'bi_weekly', 'monthly', 'quarterly', 'semi_annually', 'annually']);
            $table->integer('interval')->default(1); // Every X frequency periods
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('max_occurrences')->nullable();
            $table->integer('occurrences_created')->default(0);
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->string('currency', 3)->default('USD');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->json('template_data')->nullable(); // Store invoice/expense template
            $table->boolean('auto_send')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('accounting_customers')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('accounting_vendors')->onDelete('cascade');
            $table->index(['status', 'next_run_date']);
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_recurring_profiles');
    }
};
