<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            // Budget en Francs Congolais (FC)
            $table->decimal('budget_total_fc', 15, 2)->default(0)->after('budget_total');
            $table->decimal('budget_depense_fc', 15, 2)->default(0)->after('budget_total_fc');
            $table->decimal('budget_valide_fc', 15, 2)->default(0)->after('budget_depense_fc');
            
            // Budget en Dollars USD
            $table->decimal('budget_total_usd', 15, 2)->default(0)->after('budget_valide_fc');
            $table->decimal('budget_depense_usd', 15, 2)->default(0)->after('budget_total_usd');
            $table->decimal('budget_valide_usd', 15, 2)->default(0)->after('budget_depense_usd');
            
            // Statut budget
            $table->enum('budget_statut', ['en_attente', 'valide', 'refuse'])->default('en_attente')->after('budget_valide_usd');
            $table->timestamp('budget_valide_le')->nullable()->after('budget_statut');
            $table->foreignId('budget_valide_par')->nullable()->after('budget_valide_le')
                  ->constrained('users')->onDelete('set null');
            
            // Annulation projet
            $table->text('motif_annulation')->nullable()->change();
            $table->timestamp('annule_le')->nullable()->after('motif_annulation');
            $table->foreignId('annule_par')->nullable()->after('annule_le')
                  ->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropColumn([
                'budget_total_fc',
                'budget_depense_fc',
                'budget_valide_fc',
                'budget_total_usd',
                'budget_depense_usd',
                'budget_valide_usd',
                'budget_statut',
                'budget_valide_le',
                'annule_le'
            ]);
            
            $table->dropForeign(['budget_valide_par']);
            $table->dropColumn('budget_valide_par');
            $table->dropForeign(['annule_par']);
            $table->dropColumn('annule_par');
        });
    }
};