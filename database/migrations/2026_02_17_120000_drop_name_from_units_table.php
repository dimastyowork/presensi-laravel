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
        if (!Schema::hasColumn('units', 'name')) {
            return;
        }

        Schema::table('units', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('units', 'name')) {
            return;
        }

        Schema::table('units', function (Blueprint $table) {
            $table->string('name')->nullable()->after('sso_unit_id');
            $table->unique('name');
        });
    }
};

