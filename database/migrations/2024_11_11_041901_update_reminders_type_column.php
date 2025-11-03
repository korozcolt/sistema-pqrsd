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
        Schema::table('reminders', function (Blueprint $table) {
            // Modificar la columna reminder_type para aceptar los nuevos valores
            $table->string('reminder_type')->change(); // Quitamos el enum para usar el enum de PHP
        });
    }

    public function down()
    {
        // Primero limpiamos los datos que no coinciden con el enum
        \DB::table('reminders')
            ->whereNotIn('reminder_type', ['response_due', 'resolution_due'])
            ->delete();

        Schema::table('reminders', function (Blueprint $table) {
            $table->enum('reminder_type', ['response_due', 'resolution_due'])->change();
        });
    }
};
