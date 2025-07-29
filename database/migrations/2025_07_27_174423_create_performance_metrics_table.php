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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('employee_email');
            $table->date('metric_date');
            $table->enum('metric_type', ['daily', 'weekly', 'monthly'])->default('daily');

            // Task-related metrics
            $table->integer('tasks_assigned')->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_overdue')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0.00); // Percentage

            // Time-related metrics
            $table->integer('total_hours_worked')->default(0);
            $table->integer('estimated_hours')->default(0);
            $table->integer('actual_hours')->default(0);
            $table->decimal('efficiency_rate', 5, 2)->default(0.00); // Actual vs Estimated

            // Quality metrics
            $table->integer('tasks_on_time')->default(0);
            $table->integer('tasks_delayed')->default(0);
            $table->decimal('on_time_delivery_rate', 5, 2)->default(0.00);

            // Performance scores
            $table->decimal('productivity_score', 5, 2)->default(0.00);
            $table->decimal('quality_score', 5, 2)->default(0.00);
            $table->decimal('overall_score', 5, 2)->default(0.00);

            $table->json('additional_metrics')->nullable();
            $table->timestamps();

            $table->unique(['employee_name', 'metric_date', 'metric_type']);
            $table->index(['employee_name', 'metric_date']);
            $table->index(['metric_date', 'metric_type']);
            $table->index('overall_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
