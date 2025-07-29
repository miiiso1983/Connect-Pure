<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounting_expense_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained('accounting_expenses')->onDelete('cascade');
            $table->foreignId('tax_id')->constrained('accounting_taxes')->onDelete('cascade');
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->timestamps();

            // Indexes
            $table->unique(['expense_id', 'tax_id']);
            $table->index('expense_id');
            $table->index('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_expense_taxes');
    }
};
