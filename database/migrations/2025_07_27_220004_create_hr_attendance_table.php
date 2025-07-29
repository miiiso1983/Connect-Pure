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
        Schema::create('hr_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->date('date');
            
            // Time Tracking
            $table->time('scheduled_in')->default('09:00:00');
            $table->time('scheduled_out')->default('17:00:00');
            $table->timestamp('actual_in')->nullable();
            $table->timestamp('actual_out')->nullable();
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            
            // Calculated Fields
            $table->integer('total_hours')->nullable(); // in minutes
            $table->integer('overtime_hours')->default(0); // in minutes
            $table->integer('late_minutes')->default(0);
            $table->integer('early_departure_minutes')->default(0);
            
            // Status
            $table->enum('status', [
                'present', 'absent', 'late', 'half_day', 'on_leave', 
                'holiday', 'weekend', 'sick', 'excused'
            ])->default('present');
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->string('location')->nullable(); // office, remote, field
            $table->string('ip_address')->nullable();
            $table->json('check_in_location')->nullable(); // GPS coordinates
            $table->json('check_out_location')->nullable(); // GPS coordinates
            
            // Approval
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('hr_employees')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->unique(['employee_id', 'date']);
            $table->index(['employee_id', 'date']);
            $table->index(['date', 'status']);
            $table->index(['status']);
            $table->index(['is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_attendance');
    }
};
