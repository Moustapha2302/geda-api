<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà
            if (!Schema::hasColumn('documents', 'ocr_text')) {
                $table->longText('ocr_text')->nullable()->after('file_path');
            }

            if (!Schema::hasColumn('documents', 'ocr_status')) {
                $table->string('ocr_status')->default('pending')->after('status');
            }

            if (!Schema::hasColumn('documents', 'ocr_processed_at')) {
                $table->timestamp('ocr_processed_at')->nullable()->after('ocr_status');
            }

            if (!Schema::hasColumn('documents', 'ocr_error')) {
                $table->text('ocr_error')->nullable()->after('ocr_processed_at');
            }

            if (!Schema::hasColumn('documents', 'ocr_confidence')) {
                $table->decimal('ocr_confidence', 5, 2)->nullable()->after('ocr_error');
            }
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'ocr_text',
                'ocr_status',
                'ocr_processed_at',
                'ocr_error',
                'ocr_confidence'
            ]);
        });
    }
};


