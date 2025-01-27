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
        if (!Schema::hasColumn('content_types', 'status')) {
            Schema::table('content_types', function (Blueprint $table) {
                $table->string('status')->default('active')->after('schema');
            });
        }

        if (!Schema::hasColumn('menus', 'status')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('status')->default('active');
            });
        }

        if (!Schema::hasColumn('menu_items', 'status')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->string('status')->default('active');
            });
        }

        if (!Schema::hasColumn('contents', 'status')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->string('status')->default('active');
            });
        }

        if (!Schema::hasColumn('widgets', 'status')) {
            Schema::table('widgets', function (Blueprint $table) {
                $table->string('status')->default('active');
            });
        }
    }

    public function down(): void
    {
        Schema::table('content_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
