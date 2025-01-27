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
        Schema::table('content_types', function (Blueprint $table) {
            $table->string('status')->default('active')->after('schema');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->string('status')->default('active');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('status')->default('active');
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->string('status')->default('active');
        });

        Schema::table('widgets', function (Blueprint $table) {
            $table->string('status')->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_types', function (Blueprint $table) {
            //
        });
    }
};
