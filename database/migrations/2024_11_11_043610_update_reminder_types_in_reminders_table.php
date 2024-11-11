<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // Mapeo de valores antiguos a nuevos
        DB::table('reminders')
            ->where('reminder_type', 'resolution_due')
            ->update(['reminder_type' => 'day_before_resolution']);

        DB::table('reminders')
            ->where('reminder_type', 'response_due')
            ->update(['reminder_type' => 'day_before_response']);

        // Si hay otros valores que necesiten ser actualizados, agrégalos aquí
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Revertir los cambios si es necesario
        DB::table('reminders')
            ->where('reminder_type', 'day_before_resolution')
            ->update(['reminder_type' => 'resolution_due']);

        DB::table('reminders')
            ->where('reminder_type', 'day_before_response')
            ->update(['reminder_type' => 'response_due']);
    }
};
