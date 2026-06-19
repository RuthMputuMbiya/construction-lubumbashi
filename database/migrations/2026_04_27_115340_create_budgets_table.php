<?php
// database/migrations/2026_04_27_115340_create_budgets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EXPLICATION: Table de gestion budgétaire des projets
     * Un projet = un budget (relation one-to-one)
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id('Idbudget'); // Clé primaire personnalisée
            
            // Montants principaux
            $table->decimal('montanttotal', 15, 2);
            $table->decimal('depenses', 15, 2)->default(0);
            $table->decimal('reste', 15, 2)->virtualAs('montanttotal - depenses');
            
            // Dépenses par catégorie
            $table->decimal('depenses_materiaux', 15, 2)->default(0);
            $table->decimal('depenses_main_oeuvre', 15, 2)->default(0);
            $table->decimal('depenses_equipement', 15, 2)->default(0);
            $table->decimal('depenses_transport', 15, 2)->default(0);
            $table->decimal('depenses_administratives', 15, 2)->default(0);
            $table->decimal('depenses_imprevu', 15, 2)->default(0);
            
            // Engagements
            $table->decimal('engagements', 15, 2)->default(0);
            $table->decimal('reel_vs_prevue', 15, 2)->nullable();
            $table->decimal('taux_consommation', 5, 2)->nullable();
            
            // Informations complémentaires
            $table->json('echeancier_paiements')->nullable();
            $table->text('observations_budget')->nullable();
            $table->enum('statut_budget', ['normal', 'alerte', 'critique', 'audit'])->default('normal');
            
            // Révisions
            $table->integer('nombre_revisions')->default(0);
            $table->json('historique_revisions')->nullable();
            
            // Validations
            $table->timestamp('valide_le')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users', 'id')->onDelete('set null');
            
            // Clé étrangère vers projets - CORRECTION: référencer 'id'
            $table->foreignId('idprojet')->constrained('projets', 'id')->onDelete('cascade')->unique();
            
            $table->timestamps();
            
            // Index
            $table->index('statut_budget');
            $table->index('taux_consommation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};