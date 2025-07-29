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
        Schema::create('hr_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('hr_employees')->onDelete('cascade');
            $table->enum('review_period', ['quarterly', 'semi_annual', 'annual']);
            $table->date('review_date');

            // Rating fields (1-5 scale)
            $table->tinyInteger('technical_skills')->nullable();
            $table->tinyInteger('communication_skills')->nullable();
            $table->tinyInteger('teamwork')->nullable();
            $table->tinyInteger('leadership')->nullable();
            $table->tinyInteger('problem_solving')->nullable();
            $table->tinyInteger('initiative')->nullable();
            $table->tinyInteger('punctuality')->nullable();
            $table->tinyInteger('quality_of_work')->nullable();
            $table->decimal('overall_rating', 3, 2)->nullable();

            // Text fields
            $table->text('achievements')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->json('goals')->nullable(); // Goals for current period
            $table->json('goals_next_period')->nullable(); // Goals for next period
            $table->text('reviewer_comments')->nullable();
            $table->text('employee_comments')->nullable();

            // Status and tracking
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['employee_id', 'review_period', 'review_date']);
            $table->index(['status', 'review_date']);
            $table->index(['overall_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_performance_reviews');
    }
};
