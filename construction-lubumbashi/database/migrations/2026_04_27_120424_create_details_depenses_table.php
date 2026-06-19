<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('details_depenses', function (Blueprint $table) {
            $table->id();
            
            // Références
            $table->foreignId('id_projet')->constrained('projets')->onDelete('cascade');
            $table->foreignId('id_etape')->nullable()->constrained('planifications')->onDelete('set null');
            
            // Informations dépense
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant_fc', 15, 2)->default(0);
            $table->decimal('montant_usd', 15, 2)->default(0);
            $table->date('date_depense');
            $table->enum('categorie', [
                'materiaux',
                'main_oeuvre',
                'equipement',
                'transport',
                'administratif',
                'imprevu'
            ]);
            
            // Justificatif
            $table->string('facture')->nullable();
            $table->string('fournisseur')->nullable();
            
            // Validation
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('valide_le')->nullable();
            $table->text('motif_rejet')->nullable();
            
            $table->timestamps();
            
            $table->index('categorie');
            $table->index('statut');
            $table->index('date_depense');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('details_depenses');
    }
};