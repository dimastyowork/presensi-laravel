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
    public function up(): void
    {
        Schema::table('user_shifts', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('shift_id');
        });

        DB::table('user_shifts')->update(['is_active' => true]);

        Schema::table('user_shifts', function (Blueprint $table) {
            $table->dropUnique('user_shifts_user_id_unique');
            $table->unique(['user_id', 'shift_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_shifts', function (Blueprint $table) {
            $table->dropUnique('user_shifts_user_id_shift_id_unique');
            $table->dropColumn('is_active');
            $table->unique('user_id');
        });
    }
};
