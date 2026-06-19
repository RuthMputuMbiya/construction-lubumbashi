<?php
// database/migrations/2026_04_27_115334_create_suivis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EXPLICATION: Table des suivis d'avancement
     * Enregistre l'historique des mises à jour de progression
     */
    public function up(): void
    {
        Schema::create('suivis', function (Blueprint $table) {
            $table->id('Idsuivi'); // Clé primaire personnalisée
            
            $table->date('datesuivi');
            $table->integer('progression')->default(0);
            $table->text('observation')->nullable();
            $table->decimal('budget_consomme', 12, 2)->nullable();
            $table->integer('duree_ecoulee')->nullable();
            $table->integer('duree_restante')->nullable();
            $table->text('difficultes')->nullable();
            $table->text('actions_correctives')->nullable();
            $table->json('medias')->nullable();
            $table->enum('type_mise_a_jour', ['hebdomadaire', 'exceptionnel', 'final'])->default('hebdomadaire');
            
            // Clé étrangère vers projets - CORRECTION: référencer 'id'
            $table->foreignId('idprojet')->constrained('projets', 'id')->onDelete('cascade');
            
            // Clé étrangère vers users
            $table->foreignId('cree_par')->nullable()->constrained('users', 'id')->onDelete('set null');
            
            $table->timestamps();
            
            // Index
            $table->index('datesuivi');
            $table->index('idprojet');
            $table->index(['idprojet', 'datesuivi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suivis');
    }
};