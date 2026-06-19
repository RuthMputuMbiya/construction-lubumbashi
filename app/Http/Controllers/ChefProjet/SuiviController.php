<?php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\EtapeProjet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuiviController extends Controller
{
    /**
     * Affiche le tableau de suivi de TOUS les projets avec calcul automatique
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer TOUS les projets (planifications ET projets réels)
        $projets = Projet::where('id_chef_projet', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalEtapesGlobal = 0;
        $etapesTermineesGlobal = 0;
        $etapesEnCoursGlobal = 0;
        $etapesEnRetardGlobal = 0;
        
        // Calcul des statistiques pour chaque projet
        foreach ($projets as $projet) {
            $etapes = EtapeProjet::where('id_projet', $projet->id)->get();
            
            $projet->total_etapes = $etapes->count();
            $projet->etapes_terminees = $etapes->where('statut', 'termine')->count();
            $projet->etapes_en_cours = $etapes->where('statut', 'en_cours')->count();
            $projet->etapes_en_retard = $etapes->where('statut', 'en_retard')->count();
            $projet->avancement = $etapes->count() > 0 ? round($etapes->avg('progression'), 2) : 0;
            
            // Cumul global
            $totalEtapesGlobal += $projet->total_etapes;
            $etapesTermineesGlobal += $projet->etapes_terminees;
            $etapesEnCoursGlobal += $projet->etapes_en_cours;
            $etapesEnRetardGlobal += $projet->etapes_en_retard;
            
            // Déterminer le statut d'affichage
            if($projet->est_planification && !$projet->planification_validee) {
                $projet->statut_affichage = 'planification';
                $projet->statut_label = 'PLANIFICATION';
                $projet->badge_class = 'bg-info';
            } else {
                $projet->statut_affichage = $projet->statut;
                if($projet->statut == 'termine') {
                    $projet->statut_label = 'TERMINÉ';
                    $projet->badge_class = 'bg-success';
                } elseif($projet->statut == 'en_cours') {
                    $projet->statut_label = 'EN COURS';
                    $projet->badge_class = 'bg-primary';
                } elseif($projet->statut == 'annule') {
                    $projet->statut_label = 'ANNULÉ';
                    $projet->badge_class = 'bg-danger';
                } else {
                    $projet->statut_label = 'EN ATTENTE';
                    $projet->badge_class = 'bg-warning';
                }
            }
            
            // Détection de retard (uniquement pour projets réels)
            $dateFin = Carbon::parse($projet->date_fin_prevue);
            $projet->est_en_retard = ($dateFin < Carbon::now() && $projet->avancement < 100 && !$projet->est_planification);
            $projet->jours_restants = Carbon::now()->diffInDays($dateFin, false);
            if ($projet->jours_restants < 0) {
                $projet->jours_restants = abs($projet->jours_restants);
            }
            
            // Récupérer les étapes avec leurs chefs
            $projet->etapes = $etapes->sortBy('ordre');
        }
        
        // Statistiques globales (cumul de TOUTES les étapes)
        $stats_globales = [
            'total_projets' => $projets->count(),
            'total_planifications' => $projets->where('est_planification', true)->count(),
            'total_projets_reels' => $projets->where('est_planification', false)->count(),
            'total_etapes' => $totalEtapesGlobal,
            'etapes_terminees' => $etapesTermineesGlobal,
            'etapes_en_cours' => $etapesEnCoursGlobal,
            'etapes_en_retard' => $etapesEnRetardGlobal,
            'projets_termines' => $projets->where('statut', 'termine')->count(),
            'projets_en_cours' => $projets->where('statut', 'en_cours')->count(),
            'projets_en_attente' => $projets->where('statut', 'en_attente')->count(),
            'avancement_moyen' => $projets->where('est_planification', false)->count() > 0 
                ? round($projets->where('est_planification', false)->avg('avancement'), 2) 
                : 0,
        ];
        
        return view('chef-projet.suivi.index', compact('projets', 'stats_globales'));
    }
    
    /**
     * Graphique d'avancement d'un projet
     */
    public function chart($id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        
        $etapes = EtapeProjet::where('id_projet', $id)
            ->with('chefChantier')
            ->orderBy('ordre')
            ->get();
        
        $chartData = [
            'labels' => $etapes->pluck('nom_etape')->toArray(),
            'progressions' => $etapes->pluck('progression')->toArray(),
            'couleurs' => $etapes->map(function($etape) {
                if($etape->statut == 'termine') return '#10b981';
                if($etape->statut == 'en_cours') return '#3b82f6';
                if($etape->statut == 'en_retard') return '#ef4444';
                return '#6b7280';
            })->toArray(),
        ];
        
        $avancement_global = $etapes->count() > 0 ? round($etapes->avg('progression'), 2) : 0;
        
        return view('chef-projet.suivi.chart', compact('projet', 'etapes', 'chartData', 'avancement_global'));
    }
    
    /**
     * Rapport d'avancement
     */
    public function rapport($id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        
        $etapes = EtapeProjet::where('id_projet', $id)
            ->with('chefChantier')
            ->orderBy('ordre')
            ->get();
        
        $avancement_global = $etapes->count() > 0 ? round($etapes->avg('progression'), 2) : 0;
        
        $stats = [
            'total_etapes' => $etapes->count(),
            'terminees' => $etapes->where('statut', 'termine')->count(),
            'en_cours' => $etapes->where('statut', 'en_cours')->count(),
            'en_retard' => $etapes->where('statut', 'en_retard')->count(),
            'avancement' => $avancement_global,
        ];
        
        return view('chef-projet.suivi.rapport', compact('projet', 'etapes', 'avancement_global', 'stats'));
    }
}