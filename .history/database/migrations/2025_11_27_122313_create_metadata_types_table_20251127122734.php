<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('metadata_types', function (Blueprint $table) {
            $table->id();
            $table->string('service_id', 10)->index(); // s02, s04, ...
            $table->string('name', 100);
            $table->json('fields'); // définition des champs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadata_types');
    }
};
