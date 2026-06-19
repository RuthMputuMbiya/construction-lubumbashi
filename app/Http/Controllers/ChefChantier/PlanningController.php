<?php

namespace App\Http\Controllers\ChefChantier;

use App\Http\Controllers\Controller;
use App\Models\EtapeProjet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlanningController extends Controller
{
    /**
     * Affiche le planning sous forme de liste
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer toutes les étapes assignées au chef de chantier
        $query = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id);
        
        // Filtre par période
        if ($request->has('periode') && $request->periode != 'all') {
            switch ($request->periode) {
                case 'semaine':
                    $query->whereBetween('date_debut_prevue', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'mois':
                    $query->whereBetween('date_debut_prevue', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'trimestre':
                    $query->whereBetween('date_debut_prevue', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()]);
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
        
        // Toutes les étapes pour les stats
        $toutesEtapes = EtapeProjet::where('id_chef_chantier', $user->id)->get();
        
        // Statistiques complètes
        $stats = [
            'total' => $toutesEtapes->count(),
            'terminees' => $toutesEtapes->where('statut', 'termine')->count(),
            'en_cours' => $toutesEtapes->where('statut', 'en_cours')->count(),
            'en_retard' => $toutesEtapes->where('statut', 'en_retard')->count(),
            'non_commence' => $toutesEtapes->where('statut', 'a_venir')->count(),
            'semaine' => $etapes->filter(function($e) {
                return $e->date_debut_prevue && $e->date_debut_prevue->between(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
            })->count(),
            'mois' => $etapes->filter(function($e) {
                return $e->date_debut_prevue && $e->date_debut_prevue->between(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
            })->count(),
        ];
        
        // Prochaine échéance
        $stats['prochain'] = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->where('statut', '!=', 'termine')
            ->where('date_fin_prevue', '>=', Carbon::now())
            ->orderBy('date_fin_prevue', 'asc')
            ->first();
        
        return view('chef-chantier.planning.index', compact('etapes', 'planningParSemaine', 'stats'));
    }
    
    /**
     * Affiche le planning sous forme de calendrier
     */
    public function calendar()
    {
        $user = Auth::user();
        
        // Récupérer toutes les étapes pour le calendrier
        $etapes = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->orderBy('date_debut_prevue', 'asc')
            ->get();
        
        // Statistiques pour le calendrier
        $toutesEtapes = EtapeProjet::where('id_chef_chantier', $user->id)->get();
        
        $stats = [
            'total' => $toutesEtapes->count(),
            'terminees' => $toutesEtapes->where('statut', 'termine')->count(),
            'en_cours' => $toutesEtapes->where('statut', 'en_cours')->count(),
            'en_retard' => $toutesEtapes->where('statut', 'en_retard')->count(),
        ];
        
        // Préparer les données pour FullCalendar
        $events = [];
        foreach ($etapes as $etape) {
            $color = $this->getEventColor($etape->statut);
            $events[] = [
                'id' => $etape->id,
                'title' => $etape->nom_etape,
                'start' => $etape->date_debut_prevue ? $etape->date_debut_prevue->toDateString() : Carbon::now()->toDateString(),
                'end' => $etape->date_fin_prevue ? $etape->date_fin_prevue->addDay()->toDateString() : Carbon::now()->addDay()->toDateString(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'projet' => $etape->projet ? $etape->projet->nom : 'N/A',
                    'progression' => $etape->progression,
                    'statut' => $etape->statut,
                    'description' => $etape->description,
                ],
                'url' => route('chef-chantier.etapes.show', $etape->id),
            ];
        }
        
        $eventsJson = json_encode($events);
        
        return view('chef-chantier.planning.calendar', compact('eventsJson', 'stats'));
    }
    
    /**
     * Organise les étapes par semaine
     */
    private function organiserParSemaine($etapes)
    {
        $planning = [];
        
        foreach ($etapes as $etape) {
            if (!$etape->date_debut_prevue) {
                continue;
            }
            
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
        
        // Trier par semaine
        ksort($planning);
        
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
                return '#2563eb';
            case 'en_retard':
                return '#ef4444';
            default:
                return '#8b5cf6';
        }
    }
}