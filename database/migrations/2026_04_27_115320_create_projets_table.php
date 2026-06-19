<?php
// database/migrations/2026_04_27_115320_create_projets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EXPLICATION: Table des projets de construction
     * Clé primaire: 'id' (convention Laravel)
     */
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id(); // Clé primaire 'id' - IMPORTANT: c'est 'id' pas 'idprojet'
            
            $table->string('code_projet')->unique();
            $table->string('nom');
            $table->text('description');
            $table->string('lieu');
            $table->date('date_debut_prevue');
            $table->date('date_fin_prevue');
            $table->date('date_debut_reelle')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'annule'])->default('en_attente');
            $table->decimal('budget_total', 12, 2);
            $table->decimal('budget_depense', 12, 2)->default(0);
            
            // Clé étrangère vers users (chef projet propriétaire)
            $table->foreignId('id_chef_projet')->constrained('users')->onDelete('cascade');
            
            $table->text('motif_annulation')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index('statut');
            $table->index('id_chef_projet');
            $table->index('code_projet');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};