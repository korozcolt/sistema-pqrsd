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
        Schema::create('display_rules', function (Blueprint $table) {
            $table->id();
            $table->morphs('ruleable'); // Polimórfico para secciones, contenidos o widgets
            $table->string('type'); // device, time, location, user_type, etc.
            $table->json('conditions');
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
        Schema::dropIfExists('display_rules');
    }
};
