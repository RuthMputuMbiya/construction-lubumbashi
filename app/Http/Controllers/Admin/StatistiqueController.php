<?php
// app/Http/Controllers/Admin/StatistiqueController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    /**
     * CONTRÔLEUR STATISTIQUES (ADMIN)
     * 
     * EXPLICATION: Ce contrôleur affiche les statistiques des utilisateurs uniquement
     * Toutes les données sont calculées automatiquement
     */
    
    public function index()
    {
        // ========== STATISTIQUES GLOBALES ==========
        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'chefs_projet' => User::where('role', 'chef_projet')->count(),
            'chefs_chantier' => User::where('role', 'chef_chantier')->count(),
        ];
        
        // ========== ÉVOLUTION DES INSCRIPTIONS (12 derniers mois) ==========
        $evolutionData = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mois')
            ->orderBy('mois', 'asc')
            ->get();
        
        // Préparer les données pour le graphique
        $evolutionLabels = $evolutionData->pluck('mois')->map(function($mois) {
            $date = date_create($mois . '-01');
            return $date->format('M Y');
        })->toArray();
        
        $evolutionDataValues = $evolutionData->pluck('total')->toArray();
        
        // Si aucune donnée, ajouter des valeurs par défaut
        if (empty($evolutionLabels)) {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $evolutionLabels[] = $date->format('M Y');
                $evolutionDataValues[] = 0;
            }
        }
        
        // ========== DERNIERS UTILISATEURS INSCRITS ==========
        $derniersUsers = User::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // ========== STATISTIQUES SUPPLÉMENTAIRES ==========
        // Utilisateurs actifs/inactifs (si vous avez un champ status)
        $usersActifs = User::where('attributs', 'like', '%"status":"actif"%')->count();
        $usersInactifs = User::where('attributs', 'like', '%"status":"inactif"%')->count();
        
        // Nouveaux utilisateurs du mois
        $nouveauxMois = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // ========== RETOURNER LA VUE ==========
        return view('admin.statistiques.index', compact(
            'stats',
            'evolutionLabels',
            'evolutionDataValues',
            'derniersUsers',
            'usersActifs',
            'usersInactifs',
            'nouveauxMois'
        ));
    }
}