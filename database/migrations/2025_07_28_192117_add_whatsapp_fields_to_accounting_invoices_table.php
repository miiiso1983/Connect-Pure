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
        Schema::table('accounting_invoices', function (Blueprint $table) {
            $table->timestamp('whatsapp_sent_at')->nullable()->after('updated_at');
            $table->string('whatsapp_message_id')->nullable()->after('whatsapp_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_invoices', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_sent_at', 'whatsapp_message_id']);
        });
    }
};
