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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('position'); // header, footer, content, modal
            $table->foreignId('page_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->json('display_rules')->nullable(); // Reglas de visualización
            $table->json('styles')->nullable(); // Estilos CSS personalizados
            $table->boolean('is_global')->default(false);
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
        Schema::dropIfExists('sections');
    }
};
