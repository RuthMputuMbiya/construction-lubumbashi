<?php
// app/Http/Controllers/ChefChantier/DashboardController.php

namespace App\Http\Controllers\ChefChantier;

use App\Http\Controllers\Controller;
use App\Models\Planification;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * CONTRÔLEUR DASHBOARD CHEF DE CHANTIER
     */
    
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer toutes les étapes assignées au chef de chantier
        $etapes = Planification::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->orderBy('date_fin_prevue', 'asc')
            ->get();
        
        // Statistiques
        $stats = [
            'total' => $etapes->count(),
            'terminees' => $etapes->where('statut', 'termine')->count(),
            'en_cours' => $etapes->where('statut', 'en_cours')->count(),
            'en_retard' => $etapes->where('statut', 'en_retard')->count(),
            'non_commence' => $etapes->where('statut', 'non_commence')->count(),
            'avancement_moyen' => $etapes->isEmpty() ? 0 : round($etapes->avg('progression'), 1),
            'prochain' => $etapes->where('statut', '!=', 'termine')
                ->where('date_fin_prevue', '>=', now())
                ->sortBy('date_fin_prevue')
                ->first(),
        ];
        
        // Étapes urgentes (à rendre dans les 3 jours)
        $urgentes = $etapes->filter(function($etape) {
            $joursRestants = now()->diffInDays($etape->date_fin_prevue, false);
            return $etape->statut !== 'termine' && $joursRestants <= 3 && $joursRestants >= 0;
        });
        
        // Projets associés avec calcul d'avancement
        $projets = [];
        $projetsGroup = $etapes->groupBy('id_projet');
        
        foreach ($projetsGroup as $projetId => $etapesGroupe) {
            $projet = $etapesGroupe->first()->projet;
            $etapesCount = $etapesGroupe->count();
            $termineesCount = $etapesGroupe->where('statut', 'termine')->count();
            $avancement = $etapesCount > 0 ? round(($termineesCount / $etapesCount) * 100, 1) : 0;
            $joursRestants = now()->diffInDays($projet->date_fin_prevue, false);
            
            $projets[] = (object)[
                'id' => $projet->id,
                'nom' => $projet->nom,
                'lieu' => $projet->lieu,
                'etapes_count' => $etapesCount,
                'terminees_count' => $termineesCount,
                'avancement' => $avancement,
                'jours_restants' => $joursRestants,
            ];
        }
        
        return view('chef-chantier.dashboard', compact('stats', 'urgentes', 'projets'));
    }
}