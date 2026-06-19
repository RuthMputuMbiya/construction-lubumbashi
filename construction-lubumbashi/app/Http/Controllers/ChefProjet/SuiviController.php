<?php
// app/Http/Controllers/ChefProjet/SuiviController.php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Planification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuiviController extends Controller
{
    /**
     * CONTRÔLEUR DE SUIVI (CHEF PROJET)
     * 
     * 🔥 IMPORTANT: Pas de constructeur avec middleware
     * Les middlewares sont gérés dans les routes (routes/web.php)
     */
    
    // ❌ SUPPRIMEZ COMPLÈTEMENT CE CONSTRUCTEUR :
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'checkRole:chef_projet']);
    // }
    
    /**
     * Affiche la liste des projets avec leur avancement
     */
    public function index()
    {
        $user = Auth::user();
        $projets = Projet::where('id_chef_projet', $user->id)->get();
        
        $suiviData = [];
        foreach ($projets as $projet) {
            $etapes = Planification::where('id_projet', $projet->id)->get();
            $avancement = $etapes->isEmpty() ? 0 : round($etapes->sum('progression') / $etapes->count(), 2);
            $enRetard = $etapes->where('statut', 'en_retard')->count();
            
            $suiviData[] = (object)[
                'projet' => $projet,
                'avancement' => $avancement,
                'etapes_total' => $etapes->count(),
                'etapes_terminees' => $etapes->where('statut', 'termine')->count(),
                'etapes_retard' => $enRetard,
            ];
        }
        
        return view('chef-projet.suivi.index', compact('suiviData'));
    }
    
    /**
     * Affiche le graphique détaillé d'avancement pour un projet
     */
    public function chart($id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        $etapes = Planification::where('id_projet', $projet->id)->get();
        
        return view('chef-projet.suivi.chart', compact('projet', 'etapes'));
    }
    
    /**
     * Exporte le rapport d'avancement en PDF
     */
    public function export($id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        $etapes = Planification::where('id_projet', $projet->id)->get();
        
        // TODO: Générer PDF
        return redirect()->back()->with('info', 'Export PDF bientôt disponible');
    }
}