<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modifier le ENUM status pour inclure les nouvelles valeurs
        DB::statement("
            ALTER TABLE documents 
            MODIFY COLUMN status 
            ENUM('draft', 'pending', 'ocr_done', 'validated', 'rejected', 'signed', 'archived') 
            NOT NULL DEFAULT 'draft'
        ");
    }

    public function down(): void
    {
        // Revenir à l'ancien ENUM
        DB::statement("
            ALTER TABLE documents 
            MODIFY COLUMN status 
            ENUM('draft', 'pending', 'signed', 'archived') 
            NOT NULL DEFAULT 'draft'
        ");
    }
};