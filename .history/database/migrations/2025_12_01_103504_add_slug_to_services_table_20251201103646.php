<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
        });

        // Remplir automatiquement les slugs existants
        DB::table('services')->get()->each(function ($service) {
            DB::table('services')
                ->where('id', $service->id)
                ->update(['slug' => 's' . str_pad($service->id, 2, '0', STR_PAD_LEFT)]);
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
