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
        // Agregar campos necesarios a sections
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'component_name')) {
                $table->string('component_name')->nullable();
            }
            if (!Schema::hasColumn('sections', 'layout_position')) {
                $table->string('layout_position')->default('content');
            }
            if (!Schema::hasColumn('sections', 'is_editable')) {
                $table->boolean('is_editable')->default(true);
            }
        });

        // Agregar campos necesarios a contents
        Schema::table('contents', function (Blueprint $table) {
            if (!Schema::hasColumn('contents', 'component_data')) {
                $table->json('component_data')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['component_name', 'layout_position', 'is_editable']);
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('component_data');
        });
    }
};
