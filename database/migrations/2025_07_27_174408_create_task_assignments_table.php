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
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('employee_name');
            $table->string('employee_email');
            $table->string('employee_role')->nullable();
            $table->string('assigned_by');
            $table->datetime('assigned_at');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->text('assignment_notes')->nullable();
            $table->enum('assignment_status', ['assigned', 'accepted', 'in_progress', 'completed', 'rejected'])->default('assigned');
            $table->timestamps();

            $table->index(['task_id', 'employee_name']);
            $table->index(['employee_name', 'assignment_status']);
            $table->index('assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
