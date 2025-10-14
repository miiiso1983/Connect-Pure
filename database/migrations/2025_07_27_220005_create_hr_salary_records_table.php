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
        Schema::create('hr_salary_records', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number')->unique();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');

            // Period Information
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('year');
            $table->integer('month');
            $table->integer('working_days');
            $table->integer('actual_working_days');

            // Basic Salary Components
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('housing_allowance', 8, 2)->default(0);
            $table->decimal('transport_allowance', 8, 2)->default(0);
            $table->decimal('food_allowance', 8, 2)->default(0);
            $table->decimal('communication_allowance', 8, 2)->default(0);
            $table->decimal('other_allowances', 8, 2)->default(0);
            $table->json('allowance_details')->nullable();

            // Variable Components
            $table->decimal('overtime_amount', 8, 2)->default(0);
            $table->integer('overtime_hours')->default(0);
            $table->decimal('bonus', 8, 2)->default(0);
            $table->decimal('commission', 8, 2)->default(0);
            $table->json('bonus_details')->nullable();

            // Deductions
            $table->decimal('social_insurance', 8, 2)->default(0);
            $table->decimal('income_tax', 8, 2)->default(0);
            $table->decimal('loan_deduction', 8, 2)->default(0);
            $table->decimal('advance_deduction', 8, 2)->default(0);
            $table->decimal('absence_deduction', 8, 2)->default(0);
            $table->decimal('late_deduction', 8, 2)->default(0);
            $table->decimal('other_deductions', 8, 2)->default(0);
            $table->json('deduction_details')->nullable();

            // Calculated Totals
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('total_deductions', 8, 2);
            $table->decimal('net_salary', 10, 2);

            // Leave Information
            $table->integer('leave_days_taken')->default(0);
            $table->decimal('leave_deduction', 8, 2)->default(0);
            $table->integer('remaining_annual_leave')->default(0);

            // Status and Processing
            $table->enum('status', ['draft', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->foreignId('prepared_by')->constrained('hr_employees');
            $table->foreignId('approved_by')->nullable()->constrained('hr_employees')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable(); // bank_transfer, cash, check
            $table->string('payment_reference')->nullable();

            // Accounting Integration
            $table->foreignId('accounting_entry_id')->nullable()->constrained('accounting_journal_entries')->onDelete('set null');
            $table->boolean('is_posted_to_accounting')->default(false);

            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['employee_id', 'year', 'month']);
            $table->index(['employee_id', 'status']);
            $table->index(['year', 'month']);
            $table->index(['status']);
            $table->index(['payment_date']);
            $table->index(['is_posted_to_accounting']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_salary_records');
    }
};
