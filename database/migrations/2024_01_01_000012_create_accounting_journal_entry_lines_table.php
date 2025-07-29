<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('account_id');
            $table->text('description')->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->string('reference')->nullable();
            $table->integer('line_number');
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('accounting_journal_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounting_accounts')->onDelete('cascade');
            $table->index('journal_entry_id');
            $table->index('account_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_journal_entry_lines');
    }
};
