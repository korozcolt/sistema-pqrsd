<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primero eliminamos cualquier recordatorio existente para evitar problemas con la conversión
        DB::table('reminders')->truncate();

        // Luego modificamos la columna reminder_type
        Schema::table('reminders', function (Blueprint $table) {
            // Primero eliminamos la columna existente
            $table->dropColumn('reminder_type');
        });

        Schema::table('reminders', function (Blueprint $table) {
            // Luego la volvemos a crear con el enum
            $table->enum('reminder_type', ['response_due', 'resolution_due'])
                ->after('sent_to');

            // Agregamos las columnas is_read y read_at si no existen
            if (!Schema::hasColumn('reminders', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('reminder_type');
            }

            if (!Schema::hasColumn('reminders', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('sent_at');
            }
        });
    }

    public function down()
    {
        Schema::table('reminders', function (Blueprint $table) {
            // Si necesitas revertir, puedes volver a cambiar el tipo de columna
            $table->dropColumn('reminder_type');
            $table->string('reminder_type')->after('sent_to');

            // Si quieres también revertir las otras columnas
            if (Schema::hasColumn('reminders', 'is_read')) {
                $table->dropColumn('is_read');
            }

            if (Schema::hasColumn('reminders', 'read_at')) {
                $table->dropColumn('read_at');
            }
        });
    }
};
