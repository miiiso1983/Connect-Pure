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
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->string('author_name');
            $table->string('author_email');
            $table->enum('author_type', ['customer', 'support', 'technical'])->default('customer');
            $table->boolean('is_internal')->default(false); // Internal comments not visible to customers
            $table->boolean('is_solution')->default(false); // Mark comment as solution
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
            $table->index(['author_type', 'is_internal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
