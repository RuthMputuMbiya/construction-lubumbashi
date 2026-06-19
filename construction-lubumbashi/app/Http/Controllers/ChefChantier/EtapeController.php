<?php
// app/Http/Controllers/ChefChantier/EtapeController.php

namespace App\Http\Controllers\ChefChantier;

use App\Http\Controllers\Controller;
use App\Models\Planification;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EtapeController extends Controller
{
    /**
     * CONTRÔLEUR DE GESTION DES ÉTAPES (CHEF CHANTIER)
     * 
     * EXPLICATION: Permet au chef de chantier de voir ses étapes
     * et de mettre à jour l'avancement
     * 
     * 🔥 IMPORTANT: Le constructeur avec middleware a été SUPPRIMÉ
     * Les middlewares sont gérés dans les routes (routes/web.php)
     */
    
    // ❌ SUPPRIMEZ COMPLÈTEMENT CE CONSTRUCTEUR :
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'checkRole:chef_chantier']);
    // }
    
    /**
     * Liste toutes les étapes assignées au chef de chantier
     */
    public function index()
    {
        $user = Auth::user();
        
        $etapes = Planification::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->orderBy('date_fin_prevue', 'asc')
            ->paginate(15);
        
        // Statistiques par statut
        $stats = [
            'toutes' => Planification::where('id_chef_chantier', $user->id)->count(),
            'terminees' => Planification::where('id_chef_chantier', $user->id)->where('statut', 'termine')->count(),
            'en_cours' => Planification::where('id_chef_chantier', $user->id)->where('statut', 'en_cours')->count(),
            'en_retard' => Planification::where('id_chef_chantier', $user->id)->where('statut', 'en_retard')->count(),
            'non_commence' => Planification::where('id_chef_chantier', $user->id)->where('statut', 'non_commence')->count(),
        ];
        
        return view('chef-chantier.etapes.index', compact('etapes', 'stats'));
    }
    
    /**
     * Affiche les détails d'une étape spécifique
     */
    public function show($id)
    {
        $etape = Planification::with(['projet', 'chefChantier'])
            ->findOrFail($id);
        
        // Vérifier que le chef de chantier est bien le responsable
        if ($etape->id_chef_chantier !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette étape.');
        }
        
        $joursRestants = now()->diffInDays($etape->date_fin_prevue, false);
        $estEnRetard = $joursRestants < 0;
        $dureePrevue = $etape->date_debut_prevue->diffInDays($etape->date_fin_prevue);
        $dureeReelle = $etape->date_debut_reelle ? $etape->date_debut_reelle->diffInDays(now()) : 0;
        
        return view('chef-chantier.etapes.show', compact('etape', 'joursRestants', 'estEnRetard', 'dureePrevue', 'dureeReelle'));
    }
    
    /**
     * Affiche le formulaire de mise à jour d'avancement
     */
    public function edit($id)
    {
        $etape = Planification::with('projet')->findOrFail($id);
        
        if ($etape->id_chef_chantier !== Auth::id()) {
            abort(403);
        }
        
        $joursRestants = now()->diffInDays($etape->date_fin_prevue, false);
        $estEnRetard = $joursRestants < 0;
        
        return view('chef-chantier.etapes.update', compact('etape', 'joursRestants', 'estEnRetard'));
    }
    
    /**
     * Met à jour l'avancement de l'étape
     */
    public function update(Request $request, $id)
    {
        $etape = Planification::findOrFail($id);
        
        if ($etape->id_chef_chantier !== Auth::id()) {
            abort(403);
        }
        
        // Validation
        $validator = Validator::make($request->all(), [
            'progression' => 'required|integer|min:0|max:100',
            'commentaires_chantier' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Vérifier si l'étape est en retard
        $estEnRetard = now()->greaterThan($etape->date_fin_prevue) && $etape->statut !== 'termine';
        
        if ($estEnRetard && $request->progression < 100) {
            $validator = Validator::make($request->all(), [
                'justification_retard' => 'required|string|min:10',
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $etape->justification_retard = $request->justification_retard;
        }
        
        // Mise à jour de l'avancement
        $etape->progression = $request->progression;
        
        // Gestion du statut
        if ($request->progression >= 100) {
            $etape->statut = 'termine';
            $etape->date_fin_reelle = now();
            $message = "🎉 Félicitations ! L'étape '{$etape->nom_etape}' est terminée !";
        } elseif ($request->progression > 0) {
            $etape->statut = 'en_cours';
            if (!$etape->date_debut_reelle) {
                $etape->date_debut_reelle = now();
            }
            $message = "✅ Avancement mis à jour : {$etape->progression}%";
        } else {
            $etape->statut = 'non_commence';
            $message = "✅ Avancement mis à jour : {$etape->progression}%";
        }
        
        // Gestion du retard
        if ($estEnRetard && $request->progression < 100) {
            $etape->statut = 'en_retard';
            $message = "⚠️ Retard enregistré pour l'étape '{$etape->nom_etape}'. Justification prise en compte.";
        }
        
        $etape->commentaires_chantier = $request->commentaires_chantier;
        $etape->save();
        
        // Mettre à jour l'avancement du projet parent
        $this->updateProjetAvancement($etape->id_projet);
        
        return redirect()->route('chef-chantier.etapes.index')
            ->with('success', $message);
    }
    
    /**
     * Met à jour l'avancement global du projet
     */
    private function updateProjetAvancement($projetId)
    {
        $etapes = Planification::where('id_projet', $projetId)->get();
        
        if ($etapes->isEmpty()) {
            return;
        }
        
        $totalProgression = $etapes->sum('progression');
        $avancementGlobal = round($totalProgression / $etapes->count(), 2);
        
        // Mettre à jour le statut du projet si toutes les étapes sont terminées
        $toutesTerminees = $etapes->every(function($etape) {
            return $etape->progression >= 100;
        });
        
        $projet = Projet::find($projetId);
        if ($projet) {
            if ($toutesTerminees && $projet->statut !== 'termine') {
                $projet->statut = 'termine';
                $projet->date_fin_reelle = now();
                $projet->save();
            } elseif (!$toutesTerminees && $projet->statut === 'en_attente') {
                $projet->statut = 'en_cours';
                $projet->date_debut_reelle = now();
                $projet->save();
            }
        }
    }
}