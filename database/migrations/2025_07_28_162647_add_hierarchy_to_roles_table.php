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
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('sort_order');
            $table->integer('level')->default(0)->after('parent_id');
            $table->string('path')->nullable()->after('level');
            $table->boolean('inherit_permissions')->default(true)->after('path');

            $table->foreign('parent_id')->references('id')->on('roles')->onDelete('set null');
            $table->index(['parent_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'level']);
            $table->dropColumn(['parent_id', 'level', 'path', 'inherit_permissions']);
        });
    }
};
