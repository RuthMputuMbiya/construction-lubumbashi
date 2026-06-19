<?php
// app/Http/Controllers/ChefProjet/ProjetController.php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\EtapeProjet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // 🔥 Récupérer tous les projets réels (planifications exclues)
        $projets = Projet::where('id_chef_projet', $userId)
            ->where('est_planification', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // 🔥 Ajouter les statistiques automatiques
        foreach ($projets as $projet) {
            $projet->avancement = $this->calculerAvancementProjet($projet->id);
            $projet->etapes_count = EtapeProjet::where('id_projet', $projet->id)->count();
            
            // 🔥 Récupérer le chef de chantier assigné à la première étape
            $chefAssigne = EtapeProjet::where('id_projet', $projet->id)
                ->whereNotNull('id_chef_chantier')
                ->with('chefChantier')
                ->first();
            $projet->chef_nom = $chefAssigne && $chefAssigne->chefChantier ? $chefAssigne->chefChantier->nom : 'Non assigné';
        }
        
        // 🔥 STATISTIQUES AUTOMATIQUES
        $stats = [
            'total' => Projet::where('id_chef_projet', $userId)
                ->where('est_planification', false)
                ->count(),
            'en_cours' => Projet::where('id_chef_projet', $userId)
                ->where('est_planification', false)
                ->where('statut', 'en_cours')
                ->count(),
            'termines' => Projet::where('id_chef_projet', $userId)
                ->where('est_planification', false)
                ->where('statut', 'termine')
                ->count(),
            'en_attente' => Projet::where('id_chef_projet', $userId)
                ->where('est_planification', false)
                ->where('statut', 'en_attente')
                ->count(),
            'annules' => Projet::where('id_chef_projet', $userId)
                ->where('est_planification', false)
                ->where('statut', 'annule')
                ->count(),
        ];
        
        return view('chef-projet.projets.index', compact('projets', 'stats'));
    }
    
    public function show($id)
    {
        try {
            $user = Auth::user();
            $userId = $user->id;
            
            $projet = Projet::where('id_chef_projet', $userId)
                ->find($id);
            
            if (!$projet) {
                return redirect()->route('chef-projet.projets.index')
                    ->with('error', '❌ Projet non trouvé ou vous n\'avez pas accès à ce projet.');
            }
            
            $etapes = EtapeProjet::where('id_projet', $projet->id)
                ->with('chefChantier')
                ->orderBy('ordre', 'asc')
                ->get();
            
            $avancementGlobal = 0;
            $statsEtapes = [
                'total' => $etapes->count(),
                'terminees' => $etapes->where('progression', 100)->count(),
                'en_cours' => $etapes->where('progression', '>', 0)->where('progression', '<', 100)->count(),
                'a_venir' => $etapes->where('progression', 0)->count(),
            ];
            
            if ($etapes->count() > 0) {
                $totalProgression = $etapes->sum('progression');
                $avancementGlobal = round($totalProgression / $etapes->count());
            }
            
            $estEnRetard = false;
            $joursRetard = 0;
            if ($projet->date_fin_prevue && $avancementGlobal < 100) {
                $dateFin = \Carbon\Carbon::parse($projet->date_fin_prevue);
                if ($dateFin < now()) {
                    $estEnRetard = true;
                    $joursRetard = now()->diffInDays($dateFin);
                }
            }
            
            $chefsChantier = User::where('role', 'chef_chantier')
                ->orderBy('nom', 'asc')
                ->get();
            
            return view('chef-projet.projets.show', [
                'projet' => $projet,
                'etapes' => $etapes,
                'avancementGlobal' => $avancementGlobal,
                'statsEtapes' => $statsEtapes,
                'chefsChantier' => $chefsChantier,
                'estEnRetard' => $estEnRetard,
                'joursRetard' => $joursRetard
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('chef-projet.projets.index')
                ->with('error', '❌ Une erreur est survenue : ' . $e->getMessage());
        }
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
    
    /**
     * Affiche la page de validation du budget (GET)
     */
    public function validateBudgetPage($id)
    {
        try {
            $user = Auth::user();
            
            $projet = Projet::where('id_chef_projet', $user->id)
                ->find($id);
            
            if (!$projet) {
                return redirect()->route('chef-projet.projets.index')
                    ->with('error', '❌ Projet non trouvé. Veuillez vérifier que le projet a bien été créé.');
            }
            
            // Si le projet est encore une planification, le transformer automatiquement
            if ($projet->est_planification == true) {
                $projet->update([
                    'est_planification' => false,
                    'planification_validee' => true,
                    'statut' => 'en_attente',
                    'statut_planification' => 'validee',
                    'date_transformation_projet' => now(),
                    'date_debut_reelle' => $projet->date_debut_prevue,
                    'date_fin_reelle' => $projet->date_fin_prevue,
                ]);
                
                $projet->refresh();
            }
            
            // Vérifier si le projet est bien en attente de validation
            if ($projet->statut != 'en_attente') {
                return redirect()->route('chef-projet.projets.show', $projet->id)
                    ->with('info', 'ℹ️ Ce projet n\'est pas en attente de validation.');
            }
            
            $etapes = EtapeProjet::where('id_projet', $projet->id)
                ->with('chefChantier')
                ->orderBy('ordre')
                ->get();
            
            $avancementGlobal = $this->calculerAvancementProjet($projet->id);
            
            return view('chef-projet.projets.validate-budget', compact('projet', 'etapes', 'avancementGlobal'));
            
        } catch (\Exception $e) {
            return redirect()->route('chef-projet.projets.index')
                ->with('error', '❌ Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Valider le budget d'un projet (POST)
     */
    public function validateBudget(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $projet = Projet::where('id_chef_projet', $user->id)
                ->find($id);
            
            if (!$projet) {
                return redirect()->route('chef-projet.projets.index')
                    ->with('error', '❌ Projet non trouvé.');
            }
            
            if ($projet->statut == 'en_cours') {
                return redirect()->route('chef-projet.projets.show', $projet->id)
                    ->with('info', 'ℹ️ Ce projet est déjà en cours.');
            }
            
            if ($projet->statut == 'termine') {
                return redirect()->route('chef-projet.projets.show', $projet->id)
                    ->with('info', 'ℹ️ Ce projet est déjà terminé.');
            }
            
            if ($projet->statut == 'annule') {
                return redirect()->route('chef-projet.projets.show', $projet->id)
                    ->with('error', '❌ Ce projet est annulé.');
            }
            
            // ✅ VALIDER LE BUDGET
            $projet->update([
                'statut' => 'en_cours',
                'budget_valide' => true,
                'date_validation_budget' => now(),
                'valide_par' => $user->id,
                'valide_par_nom' => $user->name ?? $user->nom,
                'budget_statut' => 'valide',
                'budget_valide_le' => now(),
            ]);
            
            return redirect()->route('chef-projet.projets.show', $projet->id)
                ->with('success', '✅ Budget validé avec succès ! Le projet est maintenant en cours.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }
    
    public function cancel(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $projet = Projet::where('id_chef_projet', $user->id)
                ->find($id);
            
            if (!$projet) {
                return redirect()->route('chef-projet.projets.index')
                    ->with('error', '❌ Projet non trouvé.');
            }
            
            if ($projet->statut == 'termine') {
                return redirect()->route('chef-projet.projets.show', $projet->id)
                    ->with('warning', '⚠️ Ce projet est déjà terminé.');
            }
            
            if ($projet->statut == 'annule') {
                return redirect()->route('chef-projet.projets.show', $projet->id)
                    ->with('warning', '⚠️ Ce projet est déjà annulé.');
            }
            
            $motif = $request->motif ?? 'Annulé par le chef de projet';
            
            $projet->update([
                'statut' => 'annule',
                'date_annulation' => now(),
                'motif_annulation' => $motif,
                'annule_par' => $user->id,
                'annule_par_nom' => $user->name ?? $user->nom,
            ]);
            
            return redirect()->route('chef-projet.projets.index')
                ->with('success', '🗑️ Projet "' . $projet->nom . '" annulé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('chef-projet.projets.index')
                ->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }
}