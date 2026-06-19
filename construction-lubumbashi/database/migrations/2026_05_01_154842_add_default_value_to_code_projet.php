<?php
// database/migrations/2025_05_01_000000_add_default_value_to_code_projet.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->string('code_projet')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->string('code_projet')->nullable(false)->change();
        });
    }
};