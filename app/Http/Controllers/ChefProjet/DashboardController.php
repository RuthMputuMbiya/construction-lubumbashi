<?php
// app/Http/Controllers/ChefProjet/DashboardController.php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Planification;
use App\Models\EtapeProjet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // ==========================================
        // STATISTIQUES AUTOMATIQUES
        // ==========================================
        
        // 🔥 TOTAL PLANIFICATIONS (est_planification = true)
        $totalPlanifications = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', true)
            ->count();
        
        // Si aucune planification dans la table Projet, essayer la table Planification
        if ($totalPlanifications == 0) {
            $totalPlanifications = Planification::where('id_chef_projet', $userId)->count();
        }
        
        // 🔥 TOTAL PROJETS RÉELS (est_planification = false)
        $totalProjets = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->count();
        
        // 🔥 PROJETS PAR STATUT (uniquement les projets réels)
        $projetsEnCours = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->where('statut', 'en_cours')
            ->count();
        
        $projetsTermines = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->where('statut', 'termine')
            ->count();
        
        $projetsEnAttente = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->where('statut', 'en_attente')
            ->count();
        
        $projetsAnnules = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->where('statut', 'annule')
            ->count();
        
        // ==========================================
        // AVANCEMENT MOYEN
        // ==========================================
        
        $avancementMoyen = 0;
        $projets = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->get();
        
        if ($projets->count() > 0) {
            $totalAvancement = 0;
            foreach ($projets as $projet) {
                $avancement = $this->calculerAvancementProjet($projet->id);
                $totalAvancement += $avancement;
            }
            $avancementMoyen = round($totalAvancement / $projets->count());
        }
        
        // ==========================================
        // STATISTIQUES POUR LES CARTES
        // ==========================================
        
        $stats = [
            'total_planifications' => $totalPlanifications,
            'total_projets' => $totalProjets,
            'projets_en_cours' => $projetsEnCours,
            'projets_termines' => $projetsTermines,
            'projets_en_attente' => $projetsEnAttente,
            'projets_annules' => $projetsAnnules,
            'avancement_moyen' => $avancementMoyen,
        ];
        
        // ==========================================
        // PLANIFICATIONS
        // ==========================================
        
        // Récupérer les planifications depuis la table Projet
        $planifications = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Si aucune planification, essayer la table Planification
        if ($planifications->isEmpty()) {
            $planifications = Planification::where('id_chef_projet', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        foreach ($planifications as $planif) {
            $planif->etapes_count = EtapeProjet::where('id_projet', $planif->id)->count();
            $planif->etapes_terminees = EtapeProjet::where('id_projet', $planif->id)
                ->where('progression', 100)
                ->count();
        }
        
        // ==========================================
        // PROJETS RÉCENTS
        // ==========================================
        
        $projetsRecents = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($projetsRecents as $projet) {
            $projet->avancement = $this->calculerAvancementProjet($projet->id);
        }
        
        // ==========================================
        // ACTIVITÉS RÉCENTES
        // ==========================================
        
        $activitesRecentes = [];
        
        foreach ($planifications as $planif) {
            $activitesRecentes[] = (object) [
                'user' => $user->name ?? 'Système',
                'action' => 'a créé une planification',
                'cible' => $planif->nom ?? 'N/A',
                'date' => $planif->created_at,
                'icon' => 'clipboard-list',
                'color' => 'primary'
            ];
        }
        
        foreach ($projetsRecents as $projet) {
            $activitesRecentes[] = (object) [
                'user' => $user->name ?? 'Système',
                'action' => 'a créé un projet',
                'cible' => $projet->nom ?? 'N/A',
                'date' => $projet->created_at,
                'icon' => 'project-diagram',
                'color' => 'success'
            ];
        }
        
        usort($activitesRecentes, function($a, $b) {
            if (!isset($a->date) || !isset($b->date)) return 0;
            return $b->date <=> $a->date;
        });
        
        $activitesRecentes = array_slice($activitesRecentes, 0, 10);
        
        // ==========================================
        // GRAPHIQUE
        // ==========================================
        
        $chartData = [
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            'data' => [0, 0, 0, 0, 0, 0]
        ];
        
        try {
            $projetsParMois = DB::table('projets')
                ->select(DB::raw('MONTH(created_at) as mois'), DB::raw('count(*) as total'))
                ->where('id_chef_projet', $userId)
                ->where('est_planification', false)
                ->whereYear('created_at', date('Y'))
                ->groupBy('mois')
                ->orderBy('mois', 'asc')
                ->get();
            
            if ($projetsParMois->count() > 0) {
                $labels = [];
                $data = [];
                foreach ($projetsParMois as $item) {
                    $labels[] = date('M', mktime(0, 0, 0, $item->mois, 1));
                    $data[] = $item->total;
                }
                $chartData = [
                    'labels' => $labels,
                    'data' => $data
                ];
            }
        } catch (\Exception $e) {
            // Garder les valeurs par défaut
        }
        
        return view('chef-projet.dashboard', compact(
            'stats',
            'planifications',
            'projetsRecents',
            'activitesRecentes',
            'chartData'
        ));
    }
    
    private function calculerAvancementProjet($projetId)
    {
        $etapes = EtapeProjet::where('id_projet', $projetId)->get();
        
        if ($etapes->isEmpty()) {
            return 0;
        }
        
        $totalProgression = $etapes->sum('progression');
        return round($totalProgression / $etapes->count());
    }
}