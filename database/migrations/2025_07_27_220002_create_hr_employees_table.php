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
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('first_name_ar')->nullable();
            $table->string('last_name_ar')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('national_id')->nullable();
            $table->string('passport_number')->nullable();

            // Address Information
            $table->text('address')->nullable();
            $table->text('address_ar')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('SA');

            // Employment Information
            $table->foreignId('department_id')->constrained('hr_departments');
            $table->foreignId('role_id')->constrained('hr_roles');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->date('hire_date');
            $table->date('probation_end_date')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->enum('status', ['active', 'inactive', 'terminated', 'resigned'])->default('active');
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();

            // Salary Information
            $table->decimal('basic_salary', 10, 2);
            $table->json('allowances')->nullable(); // housing, transport, etc.
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('iban')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // Documents
            $table->string('profile_photo')->nullable();
            $table->json('documents')->nullable(); // CV, certificates, etc.

            // Leave Balances
            $table->integer('annual_leave_balance')->default(21);
            $table->integer('sick_leave_balance')->default(30);
            $table->integer('emergency_leave_balance')->default(5);

            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['department_id', 'status']);
            $table->index(['role_id', 'status']);
            $table->index(['manager_id']);
            $table->index(['employment_type', 'status']);
            $table->index(['hire_date']);

            // Foreign key for manager
            $table->foreign('manager_id')->references('id')->on('hr_employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_employees');
    }
};
