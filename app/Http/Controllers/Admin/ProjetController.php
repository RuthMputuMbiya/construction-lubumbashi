<?php
// app/Http/Controllers/Admin/ProjetController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\User;
use App\Models\Planification;
use App\Models\Suivi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjetController extends Controller
{
    /**
     * CONTRÔLEUR DE GESTION DES PROJETS (ADMIN)
     */
    
    /**
     * Affiche la liste de tous les projets avec filtres
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
        
        // Filtre par chef de projet
        if ($request->has('chef_projet') && $request->chef_projet != 'all') {
            $query->where('id_chef_projet', $request->chef_projet);
        }
        
        $projets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculer l'avancement pour chaque projet
        foreach ($projets as $projet) {
            $projet->avancement = $this->calculerAvancement($projet->id);
        }
        
        // Liste des chefs de projet pour le filtre
        $chefsProjet = User::where('role', 'chef_projet')->get();
        
        // Statistiques globales
        $stats = [
            'total' => Projet::count(),
            'en_cours' => Projet::where('statut', 'en_cours')->count(),
            'termine' => Projet::where('statut', 'termine')->count(),
            'en_attente' => Projet::where('statut', 'en_attente')->count(),
            'annule' => Projet::where('statut', 'annule')->count(),
            'budget_total' => Projet::sum('budget_total'),
            'budget_depense' => Projet::sum('budget_depense'),
            'avancement_moyen' => 0,
        ];
        
        // Calculer l'avancement moyen
        $totalAvancement = 0;
        foreach ($projets as $projet) {
            $totalAvancement += $projet->avancement;
        }
        $stats['avancement_moyen'] = $projets->count() > 0 ? round($totalAvancement / $projets->count(), 1) : 0;
        
        return view('admin.projets.index', compact('projets', 'stats', 'chefsProjet'));
    }
    
    /**
     * Affiche les détails d'un projet spécifique (admin)
     */
    public function show($id)
    {
        $projet = Projet::with(['chefProjet', 'planifications.chefChantier', 'budgetValidateur', 'annulePar'])
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
        
        // Données budgétaires
        $budgetInfo = [
            'total_fc' => $projet->budget_total_fc,
            'total_usd' => $projet->budget_total_usd,
            'depense_fc' => $projet->budget_depense_fc,
            'depense_usd' => $projet->budget_depense_usd,
            'valide_fc' => $projet->budget_valide_fc,
            'valide_usd' => $projet->budget_valide_usd,
            'statut' => $projet->budget_statut,
            'valide_le' => $projet->budget_valide_le,
            'valide_par' => $projet->budgetValidateur->nom ?? null,
        ];
        
        // Récupérer l'historique des suivis
        $suivis = Suivi::where('idprojet', $projet->id)
            ->orderBy('datesuivi', 'desc')
            ->take(10)
            ->get();
        
        // Données pour le graphique d'évolution
        $evolutionData = $this->getEvolutionData($projet->id);
        
        return view('admin.projets.show', compact('projet', 'avancementGlobal', 'statsEtapes', 'budgetInfo', 'suivis', 'evolutionData'));
    }
    
    /**
     * Formulaire de création d'un projet (admin)
     */
    public function create()
    {
        $chefsProjet = User::where('role', 'chef_projet')->get();
        return view('admin.projets.create', compact('chefsProjet'));
    }
    
    /**
     * Enregistrer un nouveau projet (admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'lieu' => 'required|string|max:255',
            'date_debut_prevue' => 'required|date|after_or_equal:today',
            'date_fin_prevue' => 'required|date|after:date_debut_prevue',
            'budget_total_fc' => 'nullable|numeric|min:0',
            'budget_total_usd' => 'nullable|numeric|min:0',
            'id_chef_projet' => 'required|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Générer un code projet unique
        $annee = date('Y');
        $dernier = Projet::whereYear('created_at', $annee)->count() + 1;
        $code_projet = 'PRJ-' . $annee . '-' . str_pad($dernier, 4, '0', STR_PAD_LEFT);
        
        $projet = Projet::create([
            'code_projet' => $code_projet,
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'budget_total_fc' => $request->budget_total_fc ?? 0,
            'budget_total_usd' => $request->budget_total_usd ?? 0,
            'budget_total' => ($request->budget_total_fc * 0.00038) + ($request->budget_total_usd ?? 0),
            'budget_depense' => 0,
            'budget_depense_fc' => 0,
            'budget_depense_usd' => 0,
            'budget_valide_fc' => 0,
            'budget_valide_usd' => 0,
            'budget_statut' => 'en_attente',
            'statut' => 'en_attente',
            'id_chef_projet' => $request->id_chef_projet,
        ]);
        
        return redirect()->route('admin.projets.show', $projet->id)
            ->with('success', "Projet '{$projet->nom}' créé avec succès !");
    }
    
    /**
     * Formulaire d'édition d'un projet (admin)
     */
    public function edit($id)
    {
        $projet = Projet::findOrFail($id);
        $chefsProjet = User::where('role', 'chef_projet')->get();
        
        return view('admin.projets.edit', compact('projet', 'chefsProjet'));
    }
    
    /**
     * Mettre à jour un projet (admin)
     */
    public function update(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'lieu' => 'required|string|max:255',
            'date_debut_prevue' => 'required|date',
            'date_fin_prevue' => 'required|date|after:date_debut_prevue',
            'id_chef_projet' => 'required|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $projet->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'id_chef_projet' => $request->id_chef_projet,
        ]);
        
        return redirect()->route('admin.projets.show', $projet->id)
            ->with('success', "Projet '{$projet->nom}' modifié avec succès !");
    }
    
    /**
     * Annuler un projet (admin)
     */
    public function cancel(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'motif_annulation' => 'required|string|min:10',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $projet->statut = 'annule';
        $projet->motif_annulation = $request->motif_annulation;
        $projet->annule_le = now();
        $projet->annule_par = Auth::id();
        $projet->save();
        
        return redirect()->route('admin.projets.index')
            ->with('warning', "Le projet '{$projet->nom}' a été annulé.");
    }
    
    /**
     * Valider le budget d'un projet (admin)
     */
    public function validateBudget(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'budget_valide_fc' => 'nullable|numeric|min:0',
            'budget_valide_usd' => 'nullable|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $projet->budget_valide_fc = $request->budget_valide_fc ?? 0;
        $projet->budget_valide_usd = $request->budget_valide_usd ?? 0;
        $projet->budget_total = ($projet->budget_valide_fc * 0.00038) + $projet->budget_valide_usd;
        $projet->budget_statut = 'valide';
        $projet->budget_valide_le = now();
        $projet->budget_valide_par = Auth::id();
        $projet->save();
        
        return redirect()->route('admin.projets.show', $projet->id)
            ->with('success', 'Budget validé avec succès !');
    }
    
    /**
     * Supprimer un projet (admin)
     */
    public function destroy($id)
    {
        $projet = Projet::findOrFail($id);
        
        if ($projet->planifications()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer un projet qui a des étapes.');
        }
        
        $nom = $projet->nom;
        $projet->delete();
        
        return redirect()->route('admin.projets.index')
            ->with('success', "Projet '{$nom}' supprimé.");
    }
    
    /**
     * Calcule l'avancement d'un projet
     */
    private function calculerAvancement($projetId)
    {
        $etapes = Planification::where('id_projet', $projetId)->get();
        if ($etapes->isEmpty()) return 0;
        return round($etapes->sum('progression') / $etapes->count(), 2);
    }
    
    /**
     * Récupère les données d'évolution pour le graphique
     */
    private function getEvolutionData($projetId)
    {
        $suivis = Suivi::where('idprojet', $projetId)
            ->orderBy('datesuivi', 'asc')
            ->get();
        
        return [
            'labels' => $suivis->pluck('datesuivi')->map(function($date) {
                return $date->format('d/m/Y');
            })->toArray(),
            'data' => $suivis->pluck('progression')->toArray(),
        ];
    }
}