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
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('service_id')->constrained()->cascadeOnDelete();
        $table->string('role', 20)->default('agent'); // agent, chef, admin, archiviste
        $table->string('two_factor_secret')->nullable();
        $table->string('two_factor_recovery_codes')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
