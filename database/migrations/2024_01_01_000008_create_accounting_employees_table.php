<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->enum('pay_type', ['salary', 'hourly'])->default('salary');
            $table->decimal('pay_rate', 15, 2); // Annual salary or hourly rate
            $table->string('currency', 3)->default('USD');
            $table->enum('pay_frequency', ['weekly', 'bi_weekly', 'semi_monthly', 'monthly'])->default('bi_weekly');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');
            $table->string('ssn')->nullable(); // Encrypted
            $table->string('tax_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->integer('dependents')->default(0);
            $table->json('tax_withholdings')->nullable(); // Federal, state, local tax settings
            $table->json('benefits')->nullable(); // Health insurance, 401k, etc.
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('employee_number');
            $table->index('email');
            $table->index(['status', 'employment_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_employees');
    }
};
