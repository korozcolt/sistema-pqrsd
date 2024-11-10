<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ticket_logs', function (Blueprint $table) {
            // Primero eliminamos los registros que puedan tener nulls
            DB::table('ticket_logs')->whereNull('new_status')->delete();
            DB::table('ticket_logs')->whereNull('new_department_id')->delete();
            DB::table('ticket_logs')->whereNull('new_priority')->delete();

            // Luego modificamos las columnas
            $table->string('new_status')->nullable(false)->change();
            $table->foreignId('new_department_id')->nullable(false)->change();
            $table->string('new_priority')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->string('new_status')->nullable()->change();
            $table->foreignId('new_department_id')->nullable()->change();
            $table->string('new_priority')->nullable()->change();
        });
    }
};
