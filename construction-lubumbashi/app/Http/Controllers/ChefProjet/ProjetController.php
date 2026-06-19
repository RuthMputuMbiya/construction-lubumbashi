<?php
// app/Http/Controllers/ChefProjet/ProjetController.php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Planification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjetController extends Controller
{
    /**
     * CONTRÔLEUR DE GESTION DES PROJETS (CHEF PROJET)
     */
    
    /**
     * Liste des projets du chef de projet
     */
    public function index()
    {
        $user = Auth::user();
        $projets = Projet::where('id_chef_projet', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        foreach ($projets as $projet) {
            $projet->avancement = $this->calculerAvancement($projet->id);
        }
        
        return view('chef-projet.projets.index', compact('projets'));
    }
    
    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('chef-projet.projets.create');
    }
    
    /**
     * Enregistrer un nouveau projet
     * 🔥 AUCUNE CONDITION DE MONTANT MINIMUM
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
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        
        $projet = Projet::create([
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
            'id_chef_projet' => $user->id,
        ]);
        
        return redirect()->route('chef-projet.projets.show', $projet->id)
            ->with('success', "Projet '{$projet->nom}' créé avec succès !");
    }
    
    /**
     * Détails d'un projet
     */
    public function show($id)
    {
        $projet = Projet::with(['chefProjet', 'planifications.chefChantier', 'budgetValidateur', 'annulePar'])
            ->where('id_chef_projet', Auth::id())
            ->findOrFail($id);
        
        $avancementGlobal = $this->calculerAvancement($projet->id);
        
        $statsEtapes = [
            'total' => $projet->planifications->count(),
            'terminees' => $projet->planifications->where('statut', 'termine')->count(),
            'en_cours' => $projet->planifications->where('statut', 'en_cours')->count(),
            'en_retard' => $projet->planifications->where('statut', 'en_retard')->count(),
        ];
        
        return view('chef-projet.projets.show', compact('projet', 'avancementGlobal', 'statsEtapes'));
    }
    
    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        
        if (in_array($projet->statut, ['en_cours', 'termine', 'annule'])) {
            return redirect()->route('chef-projet.projets.show', $projet->id)
                ->with('error', 'Ce projet ne peut plus être modifié.');
        }
        
        return view('chef-projet.projets.edit', compact('projet'));
    }
    
    /**
     * Mettre à jour un projet
     */
    public function update(Request $request, $id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'lieu' => 'required|string|max:255',
            'date_fin_prevue' => 'required|date|after:date_debut_prevue',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $projet->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'date_fin_prevue' => $request->date_fin_prevue,
        ]);
        
        return redirect()->route('chef-projet.projets.show', $projet->id)
            ->with('success', "Projet modifié avec succès !");
    }
    
    /**
     * VALIDER LE BUDGET DU PROJET
     */
    public function validateBudget(Request $request, $id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'budget_valide_fc' => 'nullable|numeric|min:0',
            'budget_valide_usd' => 'nullable|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Valider le budget
        $projet->budget_valide_fc = $request->budget_valide_fc ?? 0;
        $projet->budget_valide_usd = $request->budget_valide_usd ?? 0;
        $projet->budget_total = ($projet->budget_valide_fc * 0.00038) + $projet->budget_valide_usd;
        $projet->budget_statut = 'valide';
        $projet->budget_valide_le = now();
        $projet->budget_valide_par = Auth::id();
        
        // Mettre à jour le statut du projet
        if ($projet->statut === 'en_attente') {
            $projet->statut = 'en_cours';
        }
        
        $projet->save();
        
        return redirect()->route('chef-projet.projets.show', $projet->id)
            ->with('success', 'Budget validé avec succès ! Le projet peut maintenant démarrer.');
    }
    
    /**
     * ANNULER LE PROJET
     */
    public function cancel(Request $request, $id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        
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
        
        return redirect()->route('chef-projet.projets.index')
            ->with('warning', "Le projet '{$projet->nom}' a été annulé.");
    }
    
    /**
     * Supprimer un projet
     */
    public function destroy($id)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($id);
        
        if ($projet->planifications()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer un projet qui a des étapes.');
        }
        
        $nom = $projet->nom;
        $projet->delete();
        
        return redirect()->route('chef-projet.projets.index')
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
}