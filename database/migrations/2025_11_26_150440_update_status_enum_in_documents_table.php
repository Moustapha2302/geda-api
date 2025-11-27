<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('pending','ocr_done','in_review','validated','rejected','archived') DEFAULT 'pending'");
}

public function down()
{
    DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('pending','ocr_done','validated','rejected','archived') DEFAULT 'pending'");
}
};
