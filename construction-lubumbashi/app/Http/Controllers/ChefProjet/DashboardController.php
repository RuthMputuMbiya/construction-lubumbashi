<?php
// app/Http/Controllers/ChefProjet/DashboardController.php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Planification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * CONTRÔLEUR DASHBOARD CHEF PROJET
     * 
     * EXPLICATION: Affiche le tableau de bord du chef de projet
     * 
     * 🔥 IMPORTANT: Le constructeur avec middleware a été SUPPRIMÉ
     * Les middlewares sont gérés dans les routes (routes/web.php)
     */
    
    // ❌ SUPPRIMEZ COMPLÈTEMENT CE CONSTRUCTEUR :
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'checkRole:chef_projet']);
    // }
    
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer tous les projets du chef de projet
        $projets = Projet::where('id_chef_projet', $user->id)->get();
        
        // Statistiques
        $stats = [
            'total_projets' => $projets->count(),
            'projets_en_cours' => $projets->where('statut', 'en_cours')->count(),
            'projets_termines' => $projets->where('statut', 'termine')->count(),
            'projets_en_attente' => $projets->where('statut', 'en_attente')->count(),
            'projets_annules' => $projets->where('statut', 'annule')->count(),
            'budget_total' => $projets->sum('budget_total'),
            'budget_depense' => $projets->sum('budget_depense'),
        ];
        
        // Avancement moyen
        $avancementMoyen = 0;
        foreach ($projets as $projet) {
            $avancementMoyen += $this->calculerAvancementProjet($projet->id);
        }
        $stats['avancement_moyen'] = $projets->count() > 0 ? round($avancementMoyen / $projets->count(), 1) : 0;
        
        // Projets récents
        $projetsRecents = $projets->sortByDesc('created_at')->take(5);
        
        // Données pour graphiques
        $chartData = [
            'labels' => $projets->pluck('nom')->toArray(),
            'avancements' => $projets->map(function($p) { return $this->calculerAvancementProjet($p->id); })->toArray(),
        ];
        
        return view('chef-projet.dashboard', compact('stats', 'projetsRecents', 'chartData'));
    }
    
    private function calculerAvancementProjet($projetId)
    {
        $etapes = Planification::where('id_projet', $projetId)->get();
        if ($etapes->isEmpty()) return 0;
        return round($etapes->sum('progression') / $etapes->count(), 2);
    }
}