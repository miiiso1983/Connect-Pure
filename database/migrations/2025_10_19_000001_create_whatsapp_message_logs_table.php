<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->nullable()->index();
            $table->string('message_id')->nullable()->index();
            $table->string('status')->nullable()->index(); // sent, delivered, read, failed, etc.
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_message_logs');
    }
};
