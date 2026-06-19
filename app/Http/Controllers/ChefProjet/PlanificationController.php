<?php
// app/Http/Controllers/ChefProjet/PlanificationController.php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\EtapeProjet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PlanificationController extends Controller
{
    /**
     * Affiche la liste des planifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Récupérer toutes les planifications (projets en mode planification)
        $mesProjets = Projet::where('id_chef_projet', $user->id)
            ->where('est_planification', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ajouter le nombre d'étapes à chaque projet
        foreach ($mesProjets as $projet) {
            $projet->etapes_count = EtapeProjet::where('id_projet', $projet->id)->count();
            $projet->etapes_terminees = EtapeProjet::where('id_projet', $projet->id)
                ->where('statut', 'termine')
                ->count();
        }

        $projet = null;
        $etapes = collect();
        $chefsChantier = User::where('role', 'chef_chantier')->orderBy('nom')->get();

        if ($request->has('idProjet') && $request->idProjet) {
            $projet = Projet::where('id_chef_projet', $user->id)->findOrFail($request->idProjet);
            $etapes = EtapeProjet::where('id_projet', $projet->id)->with('chefChantier')->orderBy('ordre')->get();
        }

        return view('chef-projet.planification.index', compact('mesProjets', 'projet', 'etapes', 'chefsChantier'));
    }

    /**
     * Affiche le formulaire de création d'une planification
     */
    public function create()
    {
        $chefsChantier = User::where('role', 'chef_chantier')->orderBy('nom')->get();
        return view('chef-projet.planification.create', compact('chefsChantier'));
    }

    /**
     * Enregistre une nouvelle planification (SANS étapes automatiques)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_projet' => 'required|string|max:255',
            'lieu' => 'required|string|max:255',
            'responsable' => 'required|string|max:255',
            'type_projet' => 'required|in:batiment,route,pont,autre',
            'priorite' => 'required|in:haute,moyenne,basse',
            'date_debut_prevue' => 'required|date',
            'date_fin_prevue' => 'required|date|after:date_debut_prevue',
            'budget_previsionnel' => 'nullable|numeric|min:0',
            'devise' => 'required|in:CDF,USD,EUR',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs.');
        }

        // Calculer la durée automatique
        $debut = Carbon::parse($request->date_debut_prevue);
        $fin = Carbon::parse($request->date_fin_prevue);
        $duree = $debut->diffInDays($fin);

        // Formater le budget avec devise
        $budgetAvecDevise = number_format($request->budget_previsionnel ?? 0, 0, ',', ' ') . ' ' . $request->devise;

        // CRÉATION DE LA PLANIFICATION (SANS ÉTAPES)
        $planification = Projet::create([
            'id_chef_projet' => Auth::id(),
            'nom' => $request->nom_projet,
            'lieu' => $request->lieu,
            'responsable' => $request->responsable,
            'type_projet' => $request->type_projet,
            'priorite' => $request->priorite,
            'description' => $request->description,
            'budget_total' => $request->budget_previsionnel ?? 0,
            'budget_previsionnel' => $request->budget_previsionnel ?? 0,
            'devise' => $request->devise,
            'budget_formate' => $budgetAvecDevise,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'duree_jours' => $duree,
            'statut' => 'planification',
            'statut_planification' => 'en_cours',
            'est_planification' => true,
            'planification_validee' => false,
        ]);

        return redirect()->route('chef-projet.planification.etapes', $planification->id)
            ->with('success', '✅ Planification créée avec succès ! Vous pouvez maintenant ajouter les étapes.');
    }

    /**
     * Affiche la page de gestion des étapes d'une planification
     */
    public function etapes($idPlanification)
    {
        $user = Auth::user();

        $planification = Projet::where('id_chef_projet', $user->id)
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $etapes = EtapeProjet::where('id_projet', $idPlanification)
            ->with('chefChantier')
            ->orderBy('ordre', 'asc')
            ->get();

        $chefsChantier = User::where('role', 'chef_chantier')
            ->orderBy('nom', 'asc')
            ->get();

        // DÉFINIR LES ÉTAPES OBLIGATOIRES
        $etapesObligatoires = [
            1 => [
                'nom' => 'ANALYSE DES BESOINS',
                'desc' => 'Identification des besoins du client, cahier des charges, objectifs du projet.'
            ],
            2 => [
                'nom' => 'ÉTUDE DU SITE',
                'desc' => 'Analyse du terrain, topographie, géotechnique, accès, réseaux existants.'
            ],
            3 => [
                'nom' => 'ÉTUDE DE FAISABILITÉ',
                'desc' => 'Analyse técnica, économique, juridique et environnementale du projet.'
            ],
            4 => [
                'nom' => 'CONCEPTION',
                'desc' => 'Phase de conception détaillée, plans, devis, planning prévisionnel.'
            ],
            5 => [
                'nom' => 'VALIDATION FINALE',
                'desc' => 'Validation finale du projet par le client et les parties prenantes.'
            ],
        ];

        // Calculer automatiquement la progression
        $progressionTotale = 0;
        foreach ($etapes as $etape) {
            $progressionTotale += $etape->progression;
        }
        $planification->progression = $etapes->count() > 0 ? round($progressionTotale / $etapes->count()) : 0;

        return view('chef-projet.planification.etapes', compact(
            'planification', 
            'etapes', 
            'chefsChantier',
            'etapesObligatoires'
        ));
    }

    /**
     * AJOUTER UNE ÉTAPE (Le chef de projet planifie manuellement)
     */
    public function ajouterEtape(Request $request, $idPlanification)
    {
        $planification = Projet::where('id_chef_projet', Auth::id())
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $validator = Validator::make($request->all(), [
            'nom_etape' => 'required|string|max:255',
            'description' => 'required|string',
            'ordre' => 'required|integer|min:1',
            'date_debut_prevue' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut_prevue',
            'id_chef_chantier' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs.');
        }

        // Vérifier si l'ordre existe déjà
        $existe = EtapeProjet::where('id_projet', $idPlanification)
            ->where('ordre', $request->ordre)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Une étape existe déjà avec cet ordre.')
                ->withInput();
        }

        // Créer l'étape avec le chef de chantier assigné
        $etape = EtapeProjet::create([
            'id_projet' => $idPlanification,
            'nom_etape' => $request->nom_etape,
            'description' => $request->description,
            'ordre' => $request->ordre,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'progression' => 0,
            'statut' => 'a_venir',
            'id_chef_chantier' => $request->id_chef_chantier,
        ]);

        return redirect()->route('chef-projet.planification.etapes', $idPlanification)
            ->with('success', '✅ Étape "' . $request->nom_etape . '" ajoutée avec succès.');
    }

    /**
     * Supprime une étape
     */
    public function supprimerEtape($idPlanification, $idEtape)
    {
        $planification = Projet::where('id_chef_projet', Auth::id())
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $etape = EtapeProjet::where('id_projet', $idPlanification)
            ->findOrFail($idEtape);

        $etape->delete();

        return redirect()->route('chef-projet.planification.etapes', $idPlanification)
            ->with('success', '🗑️ Étape supprimée.');
    }

    /**
     * Retire un chef de chantier d'une étape
     */
    public function retirerChefChantier($idPlanification, $idEtape)
    {
        $planification = Projet::where('id_chef_projet', Auth::id())
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $etape = EtapeProjet::where('id_projet', $idPlanification)
            ->findOrFail($idEtape);

        $etape->update(['id_chef_chantier' => null]);

        return redirect()->route('chef-projet.planification.etapes', $idPlanification)
            ->with('success', 'Chef de chantier retiré de cette étape.');
    }

    /**
     * MODIFIER LE CHEF DE CHANTIER D'UNE ÉTAPE
     */
    public function modifierChef(Request $request, $idPlanification, $idEtape)
    {
        $planification = Projet::where('id_chef_projet', Auth::id())
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $etape = EtapeProjet::where('id_projet', $idPlanification)
            ->findOrFail($idEtape);

        $request->validate([
            'id_chef_chantier' => 'required|exists:users,id',
        ]);

        $ancienChef = $etape->chefChantier ? $etape->chefChantier->nom : 'Non assigné';
        $nouveauChef = User::find($request->id_chef_chantier)->nom;

        $etape->update(['id_chef_chantier' => $request->id_chef_chantier]);

        return redirect()->route('chef-projet.planification.etapes', $idPlanification)
            ->with('success', '✅ Chef de chantier modifié : ' . $ancienChef . ' → ' . $nouveauChef);
    }

    /**
     * ASSIGNER UN CHEF DE CHANTIER À UNE ÉTAPE
     */
    public function assignerChef(Request $request, $idPlanification, $idEtape)
    {
        $planification = Projet::where('id_chef_projet', Auth::id())
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $etape = EtapeProjet::where('id_projet', $idPlanification)
            ->findOrFail($idEtape);

        $request->validate([
            'id_chef_chantier' => 'required|exists:users,id',
        ]);

        $nouveauChef = User::find($request->id_chef_chantier)->nom;

        $etape->update(['id_chef_chantier' => $request->id_chef_chantier]);

        return redirect()->route('chef-projet.planification.etapes', $idPlanification)
            ->with('success', '✅ Chef de chantier assigné : ' . $nouveauChef);
    }

    /**
     * AFFICHER LE FORMULAIRE D'ÉDITION D'UNE ÉTAPE
     */
    public function editEtape($idPlanification, $idEtape)
    {
        $user = Auth::user();

        $planification = Projet::where('id_chef_projet', $user->id)
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $etape = EtapeProjet::where('id_projet', $idPlanification)
            ->findOrFail($idEtape);

        $chefsChantier = User::where('role', 'chef_chantier')
            ->orderBy('nom', 'asc')
            ->get();

        return view('chef-projet.planification.edit', compact('planification', 'etape', 'chefsChantier'));
    }

    /**
     * METTRE À JOUR UNE ÉTAPE
     */
    public function updateEtape(Request $request, $idPlanification, $idEtape)
    {
        $planification = Projet::where('id_chef_projet', Auth::id())
            ->where('est_planification', true)
            ->findOrFail($idPlanification);

        $etape = EtapeProjet::where('id_projet', $idPlanification)
            ->findOrFail($idEtape);

        $validator = Validator::make($request->all(), [
            'nom_etape' => 'required|string|max:255',
            'description' => 'required|string',
            'date_debut_prevue' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut_prevue',
            'id_chef_chantier' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs.');
        }

        $etape->update([
            'nom_etape' => $request->nom_etape,
            'description' => $request->description,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'id_chef_chantier' => $request->id_chef_chantier,
        ]);

        return redirect()->route('chef-projet.planification.etapes', $idPlanification)
            ->with('success', '✅ Étape "' . $etape->nom_etape . '" modifiée.');
    }

    /**
     * TRANSFORMER UNE PLANIFICATION EN PROJET RÉEL (VALIDATION)
     * Version avec URL directe pour éviter l'erreur de route
     * ✅ Message de succès uniquement, pas de message d'erreur "Projet non trouvé"
     */
    public function transformerEnProjet($idPlanification)
    {
        try {
            $planification = Projet::where('id_chef_projet', Auth::id())
                ->where('est_planification', true)
                ->where('planification_validee', false)
                ->findOrFail($idPlanification);

            // VÉRIFICATION AUTOMATIQUE AVANT TRANSFORMATION
            $etapesCount = EtapeProjet::where('id_projet', $idPlanification)->count();

            // Vérifier s'il y a au moins 1 étape
            if ($etapesCount < 1) {
                return redirect()->back()
                    ->with('error', '❌ Vous devez avoir au moins une étape planifiée.');
            }

            // Vérifier si toutes les étapes ont un chef assigné
            $etapesSansChef = EtapeProjet::where('id_projet', $idPlanification)
                ->whereNull('id_chef_chantier')
                ->count();

            if ($etapesSansChef > 0) {
                return redirect()->back()
                    ->with('error', '❌ ' . $etapesSansChef . ' étape(s) n\'ont pas de chef de chantier assigné.');
            }

            // 🔥 TRANSFORMATION EN PROJET RÉEL
            $planification->update([
                'est_planification' => false,
                'planification_validee' => true,
                'statut' => 'en_attente',
                'statut_planification' => 'validee',
                'date_transformation_projet' => now(),
                'date_debut_reelle' => $planification->date_debut_prevue,
                'date_fin_reelle' => $planification->date_fin_prevue,
            ]);

            // ✅ REDIRECTION VERS LA PAGE DE VALIDATION DU BUDGET - URL DIRECTE
            // ✅ Message de succès uniquement
            return redirect()->to('/chef-projet/projets/' . $planification->id . '/validate-budget')
                ->with('success', '🎉 Projet créé avec succès ! Veuillez maintenant valider le budget.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ✅ Redirection avec message d'erreur clair si planification non trouvée
            return redirect()->route('chef-projet.planification.index')
                ->with('error', '❌ Planification non trouvée.');
        } catch (\Exception $e) {
            // ✅ Redirection avec message d'erreur clair
            return redirect()->back()
                ->with('error', '❌ Une erreur est survenue lors de la création du projet : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le détail d'une planification
     */
    public function show($idPlanification)
    {
        $user = Auth::user();

        $planification = Projet::where('id_chef_projet', $user->id)
            ->findOrFail($idPlanification);

        $etapes = EtapeProjet::where('id_projet', $idPlanification)
            ->with('chefChantier')
            ->orderBy('ordre')
            ->get();

        $chefsChantier = User::where('role', 'chef_chantier')->orderBy('nom')->get();

        return view('chef-projet.planification.show', compact('planification', 'etapes', 'chefsChantier'));
    }
}