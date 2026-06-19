<?php
// app/Http/Controllers/Admin/ProjetController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\User;
use App\Models\Planification;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjetController extends Controller
{
    /**
     * CONTRÔLEUR DE GESTION DES PROJETS (ADMIN)
     * 
     * EXPLICATION: Ce contrôleur permet à l'administrateur de voir tous les projets
     * et d'avoir une vue d'ensemble sur l'activité
     * 
     * 🔥 IMPORTANT: Le constructeur avec middleware a été SUPPRIMÉ
     * Les middlewares sont gérés dans les routes (routes/web.php)
     */
    
    // ❌ SUPPRIMEZ COMPLÈTEMENT CE CONSTRUCTEUR :
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'checkRole:admin']);
    // }
    
    /**
     * Affiche la liste de tous les projets
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Projet::with('chefProjet');
        
        // Filtre par statut
        if ($request->has('statut') && $request->statut != 'all') {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code_projet', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%");
            });
        }
        
        $projets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculer l'avancement pour chaque projet
        foreach ($projets as $projet) {
            $projet->avancement = $this->calculerAvancement($projet->id);
        }
        
        // Statistiques globales
        $stats = [
            'total' => Projet::count(),
            'en_cours' => Projet::where('statut', 'en_cours')->count(),
            'termine' => Projet::where('statut', 'termine')->count(),
            'en_attente' => Projet::where('statut', 'en_attente')->count(),
            'annule' => Projet::where('statut', 'annule')->count(),
            'budget_total' => Projet::sum('budget_total'),
            'budget_depense' => Projet::sum('budget_depense'),
        ];
        
        return view('admin.projets.index', compact('projets', 'stats'));
    }
    
    /**
     * Affiche les détails d'un projet spécifique
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $projet = Projet::with(['chefProjet', 'planifications.chefChantier'])
            ->findOrFail($id);
        
        // Calculer l'avancement global
        $avancementGlobal = $this->calculerAvancement($projet->id);
        
        // Statistiques des étapes
        $etapes = $projet->planifications;
        $statsEtapes = [
            'total' => $etapes->count(),
            'terminees' => $etapes->where('statut', 'termine')->count(),
            'en_cours' => $etapes->where('statut', 'en_cours')->count(),
            'en_retard' => $etapes->where('statut', 'en_retard')->count(),
            'non_commence' => $etapes->where('statut', 'non_commence')->count(),
        ];
        
        // Calculer les métriques budgétaires
        $budgetInfo = [
            'total' => $projet->budget_total,
            'depense' => $projet->budget_depense,
            'restant' => $projet->budget_total - $projet->budget_depense,
            'taux' => $projet->budget_total > 0 ? round(($projet->budget_depense / $projet->budget_total) * 100) : 0,
        ];
        
        // Récupérer l'historique des suivis
        $suivis = \App\Models\Suivi::where('idprojet', $projet->id)
            ->orderBy('datesuivi', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.projets.show', compact('projet', 'avancementGlobal', 'statsEtapes', 'budgetInfo', 'suivis'));
    }
    
    /**
     * Calcule l'avancement d'un projet
     * 
     * @param int $projetId
     * @return float
     */
    private function calculerAvancement($projetId)
    {
        $etapes = Planification::where('id_projet', $projetId)->get();
        
        if ($etapes->isEmpty()) {
            return 0;
        }
        
        $totalProgression = $etapes->sum('progression');
        return round($totalProgression / $etapes->count(), 2);
    }
}