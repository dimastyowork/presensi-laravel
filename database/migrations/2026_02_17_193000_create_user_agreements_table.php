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
        Schema::create('user_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sso_user_id')->unique();
            $table->string('nip')->nullable();
            $table->string('name')->nullable();
            $table->string('unit')->nullable();
            $table->timestamp('agreed_at');
            $table->string('agreement_version', 50)->default('v1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_agreements');
    }
};

