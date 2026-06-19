<?php
// database/migrations/2026_04_27_115328_create_planifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EXPLICATION: Table des étapes planifiées pour chaque projet
     * Une étape est assignée à un chef de chantier
     */
    public function up(): void
    {
        Schema::create('planifications', function (Blueprint $table) {
            $table->id(); // Clé primaire 'id'
            
            $table->string('nom_etape');
            $table->text('description');
            $table->integer('ordre'); // Ordre d'exécution (1,2,3...)
            $table->date('date_debut_prevue');
            $table->date('date_fin_prevue');
            $table->date('date_debut_reelle')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->integer('progression')->default(0);
            $table->enum('statut', ['non_commence', 'en_cours', 'termine', 'en_retard'])->default('non_commence');
            
            // Clés étrangères
            $table->foreignId('id_projet')->constrained('projets', 'id')->onDelete('cascade');
            $table->foreignId('id_chef_chantier')->constrained('users', 'id')->onDelete('cascade');
            
            $table->text('commentaires_chantier')->nullable();
            $table->text('justification_retard')->nullable();
            $table->timestamps();
            
            // Index
            $table->index('id_projet');
            $table->index('id_chef_chantier');
            $table->index('statut');
            $table->index('ordre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planifications');
    }
};