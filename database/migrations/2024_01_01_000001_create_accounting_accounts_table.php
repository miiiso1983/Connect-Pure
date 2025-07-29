<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->enum('type', [
                'asset', 'liability', 'equity', 'revenue', 'expense'
            ]);
            $table->enum('subtype', [
                // Assets
                'current_asset', 'fixed_asset', 'other_asset',
                // Liabilities
                'current_liability', 'long_term_liability',
                // Equity
                'equity',
                // Revenue
                'income', 'other_income',
                // Expenses
                'cost_of_goods_sold', 'expense', 'other_expense'
            ]);
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('accounting_accounts')->onDelete('set null');
            $table->index(['type', 'subtype']);
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_accounts');
    }
};
