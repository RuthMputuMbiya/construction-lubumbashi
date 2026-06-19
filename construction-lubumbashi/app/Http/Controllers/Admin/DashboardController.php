<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Projet;
use App\Models\Planification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * CONTRÔLEUR ADMIN DASHBOARD
     * 
     * EXPLICATION: Ce contrôleur gère toutes les données affichées dans le tableau de bord administrateur.
     * Il récupère les statistiques, les graphiques et les données récentes.
     * 
     * SEUL L'ADMINISTRATEUR PEUT Y ACCÉDER (via middleware checkRole dans les routes)
     * 
     * 🔥 SUPPRIMEZ LE CONSTRUCTEUR - Les middlewares sont gérés dans les routes maintenant 🔥
     */
    
    /**
     * Affiche le tableau de bord administrateur avec toutes les statistiques
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ========== STATISTIQUES GLOBALES ==========
        
        // Récupérer le nombre total d'utilisateurs par rôle
        $stats = [
            'total_users' => User::count(),                    // Tous les utilisateurs
            'admins' => User::where('role', 'admin')->count(), // Nombre d'administrateurs
            'chefs_projet' => User::where('role', 'chef_projet')->count(), // Chefs projet
            'chefs_chantier' => User::where('role', 'chef_chantier')->count(), // Chefs chantier
            'total_projets' => Projet::count(),                 // Total projets
            'projets_en_cours' => Projet::where('statut', 'en_cours')->count(), // Projets actifs
            'projets_termines' => Projet::where('statut', 'termine')->count(), // Projets finis
            'projets_annules' => Projet::where('statut', 'annule')->count(), // Projets annulés
        ];
        
        // ========== ÉVOLUTION DES INSCRIPTIONS (30 derniers jours) ==========
        
        // Récupérer le nombre d'inscriptions par jour pour les 30 derniers jours
        $inscriptions = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Préparer les données pour le graphique
        $chartData = [
            'labels' => $inscriptions->pluck('date')->map(function($date) {
                return date('d/m', strtotime($date)); // Format: 15/04
            })->toArray(),
            'data' => $inscriptions->pluck('total')->toArray(),
        ];
        
        // ========== PROJETS RÉCENTS ==========
        
        // Récupérer les 5 derniers projets créés
        $projetsRecents = Projet::with('chefProjet')  // Charger la relation chefProjet
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Ajouter un pourcentage d'avancement pour chaque projet
        foreach ($projetsRecents as $projet) {
            $projet->avancement = $this->calculerAvancementProjet($projet->id);
        }
        
        // ========== ACTIVITÉS RÉCENTES ==========
        
        // Simuler des activités récentes (à remplacer par vos logs)
        $activitesRecentes = [
            (object) [
                'user' => 'Admin',
                'action' => 'a créé un nouveau compte',
                'cible' => 'Jean Dupont',
                'date' => now()->subHours(2),
                'icon' => 'user-plus',
                'color' => 'success'
            ],
            (object) [
                'user' => 'Jean Delacroix',
                'action' => 'a créé un nouveau projet',
                'cible' => 'Construction Immeuble Kipushi',
                'date' => now()->subHours(5),
                'icon' => 'project-diagram',
                'color' => 'primary'
            ],
            (object) [
                'user' => 'Marie Kambaji',
                'action' => 'a mis à jour l\'avancement',
                'cible' => 'Projet Lubumbashi Mall (65%)',
                'date' => now()->subDay(),
                'icon' => 'chart-line',
                'color' => 'info'
            ],
            (object) [
                'user' => 'Pierre Tshibangu',
                'action' => 'a terminé une étape',
                'cible' => 'Fondations - Phase 1',
                'date' => now()->subDays(2),
                'icon' => 'check-circle',
                'color' => 'success'
            ],
        ];
        
        // ========== BUDGET GLOBAL ==========
        
        // Calculer le budget total et les dépenses
        $budgetTotal = Projet::sum('budget_total');
        $budgetDepense = Projet::sum('budget_depense');
        $budgetRestant = $budgetTotal - $budgetDepense;
        $tauxConsommation = $budgetTotal > 0 ? round(($budgetDepense / $budgetTotal) * 100) : 0;
        
        $budgetStats = [
            'total' => $budgetTotal,
            'depense' => $budgetDepense,
            'restant' => $budgetRestant,
            'taux' => $tauxConsommation,
        ];
        
        // ========== STATISTIQUES PAR MOIS ==========
        
        // Récupérer le nombre de projets créés par mois (6 derniers mois)
        $projetsParMois = Projet::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mois')
            ->orderBy('mois', 'asc')
            ->get();
        
        $projetsMoisLabels = $projetsParMois->pluck('mois')->map(function($mois) {
            $date = date_create($mois . '-01');
            return $date->format('M Y'); // Format: Avr 2024
        })->toArray();
        
        $projetsMoisData = $projetsParMois->pluck('total')->toArray();
        
        // ========== RÉPARTITION DES PROJETS PAR STATUT ==========
        
        $projetsStatut = [
            'en_attente' => Projet::where('statut', 'en_attente')->count(),
            'en_cours' => Projet::where('statut', 'en_cours')->count(),
            'termine' => Projet::where('statut', 'termine')->count(),
            'annule' => Projet::where('statut', 'annule')->count(),
        ];
        
        // Retourner la vue avec toutes les données
        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'projetsRecents',
            'activitesRecentes',
            'budgetStats',
            'projetsMoisLabels',
            'projetsMoisData',
            'projetsStatut'
        ));
    }
    
    /**
     * Calcule l'avancement d'un projet
     * 
     * @param int $projetId
     * @return float
     */
    private function calculerAvancementProjet($projetId)
    {
        $etapes = Planification::where('id_projet', $projetId)->get();
        
        if ($etapes->isEmpty()) {
            return 0;
        }
        
        $totalProgression = $etapes->sum('progression');
        return round($totalProgression / $etapes->count(), 2);
    }
}