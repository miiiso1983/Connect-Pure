<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if columns exist before adding them
        if (!Schema::hasColumn('contacts', 'position')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('position')->nullable();
            });
        }

        if (!Schema::hasColumn('contacts', 'priority')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            });
        }

        if (!Schema::hasColumn('contacts', 'city')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('city')->nullable();
            });
        }

        if (!Schema::hasColumn('contacts', 'country')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('country')->nullable();
            });
        }

        if (!Schema::hasColumn('contacts', 'deleted_at')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['position', 'priority', 'city', 'country', 'deleted_at']);
            $table->enum('type', ['lead', 'client'])->default('lead')->after('email');
            $table->datetime('next_follow_up')->nullable()->after('notes');
            $table->decimal('potential_value', 10, 2)->nullable()->after('next_follow_up');
            $table->string('assigned_to')->nullable()->change();
            $table->dropUnique(['email']);
        });
    }
};
