<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->default('fonnte');
            $table->string('target');
            $table->string('sender_name')->nullable();
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->unsignedInteger('attempt_count')->default(0);
            $table->unsignedInteger('max_attempts')->default(1);
            $table->text('error')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
