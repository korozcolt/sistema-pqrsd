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
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('component_type'); // modal, banner, slider, etc.
            $table->json('schema')->nullable(); // Campos requeridos por el componente
            $table->text('description')->nullable();
            $table->json('validation_rules')->nullable(); // Reglas de validación
            $table->json('default_settings')->nullable(); // Configuración por defecto
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_types');
    }
};
