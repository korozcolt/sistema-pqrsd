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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('sent_to')->constrained('users');
            $table->enum('reminder_type', ['response_due', 'resolution_due']);
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('read_at')->nullable();

            // Índices para consultas frecuentes
            $table->index(['sent_to', 'is_read']);
            $table->index(['ticket_id', 'reminder_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
