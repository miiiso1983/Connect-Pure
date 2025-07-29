<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_payroll', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number')->unique();
            $table->unsignedBigInteger('employee_id');
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->date('pay_date');
            $table->enum('status', ['draft', 'processed', 'paid', 'cancelled'])->default('draft');
            $table->decimal('regular_hours', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('holiday_hours', 8, 2)->default(0);
            $table->decimal('sick_hours', 8, 2)->default(0);
            $table->decimal('vacation_hours', 8, 2)->default(0);
            $table->decimal('regular_pay', 15, 2)->default(0);
            $table->decimal('overtime_pay', 15, 2)->default(0);
            $table->decimal('holiday_pay', 15, 2)->default(0);
            $table->decimal('sick_pay', 15, 2)->default(0);
            $table->decimal('vacation_pay', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('other_earnings', 15, 2)->default(0);
            $table->decimal('gross_pay', 15, 2)->default(0);
            $table->decimal('federal_tax', 15, 2)->default(0);
            $table->decimal('state_tax', 15, 2)->default(0);
            $table->decimal('local_tax', 15, 2)->default(0);
            $table->decimal('social_security', 15, 2)->default(0);
            $table->decimal('medicare', 15, 2)->default(0);
            $table->decimal('unemployment_tax', 15, 2)->default(0);
            $table->decimal('health_insurance', 15, 2)->default(0);
            $table->decimal('dental_insurance', 15, 2)->default(0);
            $table->decimal('vision_insurance', 15, 2)->default(0);
            $table->decimal('retirement_401k', 15, 2)->default(0);
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_pay', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->json('calculation_details')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('accounting_employees')->onDelete('cascade');
            $table->index(['employee_id', 'pay_period_start']);
            $table->index(['status', 'pay_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_payroll');
    }
};
