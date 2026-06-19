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
     * (inscriptions, répartition par rôle)
     */
    
    public function index()
    {
        // ========== STATISTIQUES UTILISATEURS ==========
        
        // Total des utilisateurs
        $totalUsers = User::count();
        
        // Répartition par rôle
        $admins = User::where('role', 'admin')->count();
        $chefsProjet = User::where('role', 'chef_projet')->count();
        $chefsChantier = User::where('role', 'chef_chantier')->count();
        
        // Évolution des inscriptions (12 derniers mois)
        $evolutionData = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mois')
            ->orderBy('mois', 'asc')
            ->get();
        
        $evolutionLabels = $evolutionData->pluck('mois')->map(function($mois) {
            $date = date_create($mois . '-01');
            return $date->format('M Y');
        })->toArray();
        
        $evolutionDataValues = $evolutionData->pluck('total')->toArray();
        
        // Derniers utilisateurs inscrits
        $derniersUsers = User::orderBy('created_at', 'desc')->take(10)->get();
        
        // 🔥 PASSER LES DONNÉES À LA VUE
        return view('admin.statistiques.index', [
            'totalUsers' => $totalUsers,
            'admins' => $admins,
            'chefsProjet' => $chefsProjet,
            'chefsChantier' => $chefsChantier,
            'evolutionLabels' => $evolutionLabels,
            'evolutionDataValues' => $evolutionDataValues,
            'derniersUsers' => $derniersUsers
        ]);
    }
}