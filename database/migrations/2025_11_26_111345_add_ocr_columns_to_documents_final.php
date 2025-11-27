<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ajouter les colonnes OCR si elles n'existent pas
        if (!Schema::hasColumn('documents', 'ocr_text')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->longText('ocr_text')->nullable();
            });
        }

        if (!Schema::hasColumn('documents', 'ocr_status')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('ocr_status', 50)->default('pending');
            });
        }

        if (!Schema::hasColumn('documents', 'ocr_processed_at')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->timestamp('ocr_processed_at')->nullable();
            });
        }

        if (!Schema::hasColumn('documents', 'ocr_error')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->text('ocr_error')->nullable();
            });
        }

        if (!Schema::hasColumn('documents', 'ocr_confidence')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->decimal('ocr_confidence', 5, 2)->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'ocr_text')) {
                $table->dropColumn('ocr_text');
            }
            if (Schema::hasColumn('documents', 'ocr_status')) {
                $table->dropColumn('ocr_status');
            }
            if (Schema::hasColumn('documents', 'ocr_processed_at')) {
                $table->dropColumn('ocr_processed_at');
            }
            if (Schema::hasColumn('documents', 'ocr_error')) {
                $table->dropColumn('ocr_error');
            }
            if (Schema::hasColumn('documents', 'ocr_confidence')) {
                $table->dropColumn('ocr_confidence');
            }
        });
    }
};
