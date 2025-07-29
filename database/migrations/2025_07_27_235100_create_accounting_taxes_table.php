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
        Schema::create('accounting_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('rate', 8, 4); // Supports up to 9999.9999%
            $table->enum('type', ['vat', 'gst', 'sales_tax', 'income_tax', 'withholding_tax', 'excise_tax', 'customs_duty', 'other'])->default('vat');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('country_code', 2)->nullable();
            $table->string('region')->nullable();
            $table->json('applies_to')->nullable(); // ['products', 'services', 'shipping', etc.]
            $table->enum('calculation_method', ['percentage', 'fixed_amount', 'tiered'])->default('percentage');
            $table->boolean('compound_tax')->default(false); // Tax on tax
            $table->boolean('inclusive')->default(false); // Tax included in price
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['is_active', 'type']);
            $table->index(['country_code', 'is_active']);
            $table->index(['effective_date', 'expiry_date']);
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_taxes');
    }
};
