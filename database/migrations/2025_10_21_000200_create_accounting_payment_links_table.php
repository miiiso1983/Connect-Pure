<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounting_payment_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('accounting_invoices')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('status')->default('pending'); // pending, paid, expired, cancelled
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payment_links');
    }
};

