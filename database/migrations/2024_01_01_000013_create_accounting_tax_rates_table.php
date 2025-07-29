<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code')->unique();
            $table->decimal('rate', 5, 4); // e.g., 0.0825 for 8.25%
            $table->enum('type', ['sales', 'purchase', 'payroll', 'other']);
            $table->string('jurisdiction')->nullable(); // Federal, State, Local
            $table->string('country', 2)->default('US');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_compound')->default(false); // Tax on tax
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['country', 'state']);
            $table->index('effective_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_tax_rates');
    }
};
