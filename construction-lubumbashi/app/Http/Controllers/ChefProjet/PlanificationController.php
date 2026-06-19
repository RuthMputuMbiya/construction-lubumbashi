<?php
// app/Http/Controllers/ChefProjet/PlanificationController.php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Planification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlanificationController extends Controller
{
    public function index($idProjet = null)
    {
        $mesProjets = Projet::where('id_chef_projet', Auth::id())->orderBy('created_at', 'desc')->get();
        $chefsChantier = User::where('role', 'chef_chantier')->orderBy('nom', 'asc')->get();
        $projet = null;
        $etapes = collect();
        
        if ($idProjet && $idProjet != 0) {
            $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($idProjet);
            $etapes = Planification::where('id_projet', $idProjet)->orderBy('ordre')->get();
        }
        
        return view('chef-projet.planification.index', compact('mesProjets', 'projet', 'etapes', 'chefsChantier'));
    }
    
    public function store(Request $request, $idProjet)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($idProjet);
        
        $validator = Validator::make($request->all(), [
            'nom_etape' => 'required|string|max:255',
            'description' => 'required|string',
            'ordre' => 'required|integer|min:1',
            'date_debut_prevue' => 'required|date|after_or_equal:' . $projet->date_debut_prevue,
            'date_fin_prevue' => 'required|date|after:date_debut_prevue|before_or_equal:' . $projet->date_fin_prevue,
            'id_chef_chantier' => 'required|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $etape = Planification::create([
            'nom_etape' => $request->nom_etape,
            'description' => $request->description,
            'ordre' => $request->ordre,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'progression' => 0,
            'statut' => 'non_commence',
            'id_projet' => $idProjet,
            'id_chef_chantier' => $request->id_chef_chantier,
        ]);
        
        if ($projet->statut === 'en_attente') {
            $projet->update(['statut' => 'en_cours']);
        }
        
        return redirect()->route('chef-projet.planification.index', $idProjet)
            ->with('success', "Étape ajoutée avec succès !");
    }
    
    public function destroy($idProjet, $idEtape)
    {
        $projet = Projet::where('id_chef_projet', Auth::id())->findOrFail($idProjet);
        $etape = Planification::where('id_projet', $idProjet)->findOrFail($idEtape);
        $etape->delete();
        
        return redirect()->route('chef-projet.planification.index', $idProjet)
            ->with('success', "Étape supprimée avec succès !");
    }
}