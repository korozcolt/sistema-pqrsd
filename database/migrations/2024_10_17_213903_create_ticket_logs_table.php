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
        Schema::create('ticket_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('changed_by')->constrained('users');
            $table->enum('previous_status', [
                'pending', 'in_progress', 'closed', 'resolved', 'rejected', 'reopened'
            ])->nullable();
            $table->enum('new_status', [
                'pending', 'in_progress', 'closed', 'resolved', 'rejected', 'reopened'
            ]);
            $table->foreignId('previous_department_id')->nullable()->constrained('departments');
            $table->foreignId('new_department_id')->nullable()->constrained('departments');
            $table->enum('previous_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->enum('new_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->text('change_reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();

            // Índices optimizados
            $table->index(['ticket_id', 'changed_at']);
            $table->index(['changed_by', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_logs');
    }
};
