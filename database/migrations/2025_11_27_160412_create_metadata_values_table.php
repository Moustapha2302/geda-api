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
        Schema::create('metadata_values', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('document_id');
    $table->unsignedBigInteger('metadata_type_id');
    $table->json('value'); // clé-valeur
    $table->timestamps();

    $table->foreign('document_id')->references('id')->on('documents')->cascadeOnDelete();
    $table->foreign('metadata_type_id')->references('id')->on('metadata_types')->cascadeOnDelete();
    $table->unique(['document_id', 'metadata_type_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadata_values');
    }
};
