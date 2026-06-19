<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapes_projets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_projet')->constrained('projets')->onDelete('cascade');
            $table->string('nom_etape');
            $table->text('description')->nullable();
            $table->integer('ordre');
            $table->date('date_debut_prevue');
            $table->date('date_fin_prevue');
            $table->date('date_debut_reelle')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->integer('progression')->default(0);
            $table->enum('statut', ['a_venir', 'en_cours', 'termine', 'en_retard'])->default('a_venir');
            $table->foreignId('id_chef_chantier')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapes_projets');
    }
};