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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('status', [
                'pending', 'in_progress', 'closed', 'resolved', 'rejected', 'reopened'
            ])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('type', ['petition', 'complaint', 'claim', 'suggestion']);
            $table->date('response_due_date')->nullable();
            $table->date('resolution_due_date')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolution_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices compuestos para queries comunes
            $table->index(['status', 'priority', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index(['ticket_number']);
            $table->index(['type', 'status']); // Índice adicional para filtrado por tipo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
