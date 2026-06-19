<?php
// app/Http/Controllers/ChefChantier/PlanningController.php

namespace App\Http\Controllers\ChefChantier;

use App\Http\Controllers\Controller;
use App\Models\Planification;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanningController extends Controller
{
    /**
     * CONTRÔLEUR DE PLANNING (CHEF CHANTIER)
     * 
     * EXPLICATION: Ce contrôleur gère l'affichage du planning personnel
     * du chef de chantier avec différentes vues (liste, calendrier)
     * 
     * 🔥 IMPORTANT: Pas de constructeur avec middleware
     * Les middlewares sont gérés dans les routes (routes/web.php)
     */
    
    // ❌ SUPPRIMEZ COMPLÈTEMENT CE CONSTRUCTEUR :
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'checkRole:chef_chantier']);
    // }
    
    /**
     * Affiche le planning sous forme de liste
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer toutes les étapes assignées
        $query = Planification::with('projet')
            ->where('id_chef_chantier', $user->id);
        
        // Filtre par période
        if ($request->has('periode')) {
            switch ($request->periode) {
                case 'semaine':
                    $query->whereBetween('date_debut_prevue', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'mois':
                    $query->whereBetween('date_debut_prevue', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'trimestre':
                    $query->whereBetween('date_debut_prevue', [now()->startOfQuarter(), now()->endOfQuarter()]);
                    break;
            }
        }
        
        // Filtre par statut
        if ($request->has('statut') && $request->statut != 'all') {
            $query->where('statut', $request->statut);
        }
        
        $etapes = $query->orderBy('date_debut_prevue', 'asc')->get();
        
        // Organiser les étapes par semaine
        $planningParSemaine = $this->organiserParSemaine($etapes);
        
        // Statistiques
        $stats = [
            'total' => $etapes->count(),
            'semaine' => $etapes->filter(function($e) {
                return $e->date_debut_prevue->between(now()->startOfWeek(), now()->endOfWeek());
            })->count(),
            'mois' => $etapes->filter(function($e) {
                return $e->date_debut_prevue->between(now()->startOfMonth(), now()->endOfMonth());
            })->count(),
            'prochain' => $etapes->filter(function($e) {
                return $e->date_debut_prevue >= now() && $e->statut !== 'termine';
            })->first(),
        ];
        
        return view('chef-chantier.planning.index', compact('etapes', 'planningParSemaine', 'stats'));
    }
    
    /**
     * Affiche le planning sous forme de calendrier
     * 
     * @return \Illuminate\View\View
     */
    public function calendar()
    {
        $user = Auth::user();
        
        // Récupérer toutes les étapes pour le calendrier
        $etapes = Planification::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->orderBy('date_debut_prevue', 'asc')
            ->get();
        
        // Préparer les données pour FullCalendar
        $events = [];
        foreach ($etapes as $etape) {
            $color = $this->getEventColor($etape->statut);
            $events[] = [
                'id' => $etape->id,
                'title' => $etape->nom_etape,
                'start' => $etape->date_debut_prevue->toDateString(),
                'end' => $etape->date_fin_prevue->addDay()->toDateString(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'projet' => $etape->projet->nom,
                    'progression' => $etape->progression,
                    'statut' => $etape->statut,
                    'description' => $etape->description,
                ],
                'url' => route('chef-chantier.etapes.show', $etape->id),
            ];
        }
        
        $eventsJson = json_encode($events);
        
        return view('chef-chantier.planning.calendar', compact('eventsJson'));
    }
    
    /**
     * Organise les étapes par semaine
     */
    private function organiserParSemaine($etapes)
    {
        $planning = [];
        
        foreach ($etapes as $etape) {
            $semaine = $etape->date_debut_prevue->format('W-Y');
            if (!isset($planning[$semaine])) {
                $planning[$semaine] = [
                    'semaine' => $semaine,
                    'date_debut' => $etape->date_debut_prevue->copy()->startOfWeek(),
                    'date_fin' => $etape->date_debut_prevue->copy()->endOfWeek(),
                    'etapes' => []
                ];
            }
            $planning[$semaine]['etapes'][] = $etape;
        }
        
        return $planning;
    }
    
    /**
     * Retourne la couleur selon le statut
     */
    private function getEventColor($statut)
    {
        switch ($statut) {
            case 'termine':
                return '#10b981';
            case 'en_cours':
                return '#3b82f6';
            case 'en_retard':
                return '#ef4444';
            default:
                return '#8b5cf6';
        }
    }
    
    /**
     * Exporte le planning en PDF
     */
    public function export()
    {
        $user = Auth::user();
        
        $etapes = Planification::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->where('statut', '!=', 'termine')
            ->orderBy('date_debut_prevue', 'asc')
            ->get();
        
        // TODO: Utiliser une librairie PDF comme barryvdh/laravel-dompdf
        return redirect()->back()->with('info', 'Fonctionnalité d\'export PDF bientôt disponible');
    }
}