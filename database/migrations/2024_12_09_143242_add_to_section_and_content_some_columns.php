<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Solo agregamos campos que son estrictamente necesarios
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'type')) {
                $table->string('type')->after('name')->nullable();  // Para identificar si es menu, slider, etc.
            }
            if (!Schema::hasColumn('sections', 'config')) {
                $table->json('config')->after('settings')->nullable();  // Para configuración específica del tipo
            }
        });

        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'template')) {
                $table->string('template')->after('layout')->default('default');  // Para manejo de diferentes templates
            }
        });
    }

    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['type', 'config']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
