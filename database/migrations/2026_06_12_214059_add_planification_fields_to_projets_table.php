<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projets', function (Blueprint $table) {
            // Champs pour la planification
            if (!Schema::hasColumn('projets', 'responsable')) {
                $table->string('responsable')->nullable()->after('description');
            }
            if (!Schema::hasColumn('projets', 'type_projet')) {
                $table->enum('type_projet', ['batiment', 'route', 'pont', 'autre'])->default('batiment')->after('responsable');
            }
            if (!Schema::hasColumn('projets', 'priorite')) {
                $table->enum('priorite', ['haute', 'moyenne', 'basse'])->default('moyenne')->after('type_projet');
            }
            if (!Schema::hasColumn('projets', 'statut_planification')) {
                $table->enum('statut_planification', ['en_cours', 'validee'])->default('en_cours')->after('priorite');
            }
            if (!Schema::hasColumn('projets', 'duree_jours')) {
                $table->integer('duree_jours')->nullable()->after('date_fin_prevue');
            }
            if (!Schema::hasColumn('projets', 'est_planification')) {
                $table->boolean('est_planification')->default(true)->after('statut');
            }
            if (!Schema::hasColumn('projets', 'planification_validee')) {
                $table->boolean('planification_validee')->default(false)->after('est_planification');
            }
            if (!Schema::hasColumn('projets', 'date_transformation_projet')) {
                $table->timestamp('date_transformation_projet')->nullable()->after('planification_validee');
            }
        });
    }

    public function down()
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropColumn([
                'responsable', 'type_projet', 'priorite', 
                'statut_planification', 'duree_jours',
                'est_planification', 'planification_validee', 
                'date_transformation_projet'
            ]);
        });
    }
};