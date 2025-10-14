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
        Schema::create('hr_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');

            // Leave Details
            $table->enum('leave_type', [
                'annual', 'sick', 'emergency', 'maternity', 'paternity',
                'unpaid', 'study', 'hajj', 'bereavement', 'other',
            ]);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->boolean('is_half_day')->default(false);
            $table->enum('half_day_period', ['morning', 'afternoon'])->nullable();

            // Request Information
            $table->text('reason');
            $table->text('reason_ar')->nullable();
            $table->string('contact_during_leave')->nullable();
            $table->json('attachments')->nullable(); // medical certificates, etc.

            // Approval Workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('approver_id')->nullable()->constrained('hr_employees')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // HR Processing
            $table->boolean('is_paid')->default(true);
            $table->decimal('deduction_amount', 8, 2)->default(0);
            $table->foreignId('processed_by')->nullable()->constrained('hr_employees')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'status']);
            $table->index(['leave_type', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['approver_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_leave_requests');
    }
};
