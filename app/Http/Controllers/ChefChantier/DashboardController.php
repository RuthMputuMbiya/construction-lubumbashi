<?php

namespace App\Http\Controllers\ChefChantier;

use App\Http\Controllers\Controller;
use App\Models\EtapeProjet;  // ← CHANGEMENT: Utilisez EtapeProjet, pas Planification
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer toutes les étapes assignées au chef de chantier (CORRIGÉ)
        $etapes = EtapeProjet::with('projet')  // ← CHANGEMENT: EtapeProjet au lieu de Planification
            ->where('id_chef_chantier', $user->id)
            ->orderBy('date_fin_prevue', 'asc')
            ->get();
        
        // Statistiques
        $stats = [
            'total' => $etapes->count(),
            'terminees' => $etapes->where('statut', 'termine')->count(),
            'en_cours' => $etapes->where('statut', 'en_cours')->count(),
            'en_retard' => $etapes->where('statut', 'en_retard')->count(),
            'a_venir' => $etapes->where('statut', 'a_venir')->count(),
            'avancement_moyen' => $etapes->isEmpty() ? 0 : round($etapes->avg('progression'), 1),
        ];
        
        // Prochaine échéance
        $stats['prochain'] = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->where('statut', '!=', 'termine')
            ->where('date_fin_prevue', '>=', now())
            ->orderBy('date_fin_prevue', 'asc')
            ->first();
        
        // Étapes urgentes
        $urgentes = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->where('statut', '!=', 'termine')
            ->where(function($query) {
                $query->where('statut', 'en_retard')
                    ->orWhere('date_fin_prevue', '<=', now()->addDays(3));
            })
            ->orderBy('date_fin_prevue', 'asc')
            ->get();
        
        // Récupérer les projets UNIQUES à partir des étapes assignées
        $projetsIds = $etapes->pluck('id_projet')->unique();
        
        $projets = [];
        foreach ($projetsIds as $projetId) {
            $projet = Projet::find($projetId);
            if ($projet) {
                $etapesProjet = $etapes->where('id_projet', $projetId);
                $termineesCount = $etapesProjet->where('statut', 'termine')->count();
                $totalCount = $etapesProjet->count();
                $avancement = $totalCount > 0 ? round(($termineesCount / $totalCount) * 100, 1) : 0;
                
                // Calcul des jours restants
                $joursRestants = now()->diffInDays($projet->date_fin_prevue, false);
                
                $projets[] = (object)[
                    'id' => $projet->id,
                    'nom' => $projet->nom,
                    'lieu' => $projet->lieu,
                    'etapes_count' => $totalCount,
                    'terminees_count' => $termineesCount,
                    'avancement' => $avancement,
                    'jours_restants' => (int)$joursRestants,
                ];
            }
        }
        
        return view('chef-chantier.dashboard', compact('stats', 'urgentes', 'projets'));
    }
}