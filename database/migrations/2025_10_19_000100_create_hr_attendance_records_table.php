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
        if (! Schema::hasTable('hr_attendance_records')) {
            Schema::create('hr_attendance_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->nullable()->index();
                $table->date('date')->nullable()->index();
                $table->string('status')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_attendance_records');
    }
};

