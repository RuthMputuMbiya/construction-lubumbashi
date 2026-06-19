<?php

namespace App\Http\Controllers\ChefChantier;

use App\Http\Controllers\Controller;
use App\Models\EtapeProjet;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EtapeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id);
        
        if ($request->filled('projet')) {
            $query->where('id_projet', $request->projet);
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        $etapes = $query->orderBy('date_fin_prevue', 'asc')->get();
        
        $toutesEtapes = EtapeProjet::where('id_chef_chantier', $user->id)->get();
        
        $stats = [
            'toutes' => $toutesEtapes->count(),
            'terminees' => $toutesEtapes->where('statut', 'termine')->count(),
            'en_cours' => $toutesEtapes->where('statut', 'en_cours')->count(),
            'en_retard' => $toutesEtapes->where('statut', 'en_retard')->count(),
        ];
        
        return view('chef-chantier.etapes.index', compact('etapes', 'stats'));
    }
    
    public function show($id)
    {
        $user = Auth::user();
        
        $etape = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->findOrFail($id);
        
        $dateDebut = Carbon::parse($etape->date_debut_prevue);
        $dateFin = Carbon::parse($etape->date_fin_prevue);
        $dureePrevue = $dateDebut->diffInDays($dateFin);
        
        $aujourdhui = Carbon::now();
        $joursRestants = $aujourdhui->diffInDays($dateFin, false);
        if ($joursRestants < 0) {
            $joursRestants = 0;
        }
        
        $estEnRetard = ($etape->statut == 'en_retard') || 
                       ($etape->statut != 'termine' && $dateFin < $aujourdhui);
        
        $dureeReelle = 0;
        if ($etape->date_debut_reelle && $etape->date_fin_reelle) {
            $debutReel = Carbon::parse($etape->date_debut_reelle);
            $finReel = Carbon::parse($etape->date_fin_reelle);
            $dureeReelle = $debutReel->diffInDays($finReel);
        }
        
        return view('chef-chantier.etapes.show', compact(
            'etape', 'dureePrevue', 'joursRestants', 'estEnRetard', 'dureeReelle'
        ));
    }
    
    public function edit($id)
    {
        $user = Auth::user();
        
        $etape = EtapeProjet::with('projet')
            ->where('id_chef_chantier', $user->id)
            ->findOrFail($id);
        
        return view('chef-chantier.etapes.edit', compact('etape'));
    }
    
    /**
     * METTRE À JOUR L'AVANCEMENT
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $etape = EtapeProjet::where('id_chef_chantier', $user->id)
            ->findOrFail($id);
        
        $request->validate([
            'progression' => 'required|integer|min:0|max:100',
            'statut' => 'required|in:a_venir,en_cours,termine,en_retard',
        ]);
        
        $ancienneProgression = $etape->progression;
        $ancienStatut = $etape->statut;
        
        $etape->progression = $request->progression;
        $etape->statut = $request->statut;
        
        if ($request->filled('commentaire')) {
            $etape->commentaires_chantier = $request->commentaire;
        }
        
        if ($request->statut == 'en_cours' && is_null($etape->date_debut_reelle)) {
            $etape->date_debut_reelle = now();
        }
        
        if ($request->statut == 'termine' && $request->progression >= 100) {
            $etape->date_fin_reelle = now();
        }
        
        $etape->save();
        
        // Mettre à jour l'avancement du projet
        $this->updateProjetStatus($etape->id_projet);
        
        $message = "✅ Avancement mis à jour : {$ancienneProgression}% → {$etape->progression}%";
        if ($ancienStatut != $etape->statut) {
            $message .= " | Statut: " . $this->getStatutLabel($ancienStatut) . " → " . $this->getStatutLabel($etape->statut);
        }
        
        return redirect()->route('chef-chantier.etapes.show', $etape->id)
            ->with('success', $message);
    }
    
    /**
     * TERMINER UNE ÉTAPE DIRECTEMENT (bouton spécial)
     * Met l'étape à 100% et vérifie si le projet est complété
     */
    public function terminer($id)
    {
        $user = Auth::user();
        
        // Récupérer l'étape assignée au chef de chantier connecté
        $etape = EtapeProjet::where('id_chef_chantier', $user->id)
            ->findOrFail($id);
        
        // Sauvegarder les anciennes valeurs pour le message
        $ancienneProgression = $etape->progression;
        $ancienStatut = $etape->statut;
        $nomEtape = $etape->nom_etape;
        $ordre = $etape->ordre;
        
        // Marquer comme terminée à 100%
        $etape->progression = 100;
        $etape->statut = 'termine';
        $etape->date_fin_reelle = now();
        
        // Si l'étape n'avait pas de date de début, la mettre maintenant
        if (is_null($etape->date_debut_reelle)) {
            $etape->date_debut_reelle = now();
        }
        
        $etape->save();
        
        // Mettre à jour l'avancement du projet et vérifier si le projet est terminé
        $projetTermine = $this->updateProjetStatus($etape->id_projet);
        
        // Message selon que le projet est terminé ou non
        if ($projetTermine) {
            return redirect()->route('chef-chantier.etapes.index')
                ->with('success', '🎉 FÉLICITATIONS ! Étape "' . $nomEtape . '" terminée ET le projet est complété à 100% ! 🎉');
        }
        
        return redirect()->route('chef-chantier.etapes.index')
            ->with('success', '✅ Étape "' . $nomEtape . '" terminée avec succès !');
    }
    
    /**
     * METTRE À JOUR LE STATUT DU PROJET
     * Vérifie si toutes les étapes sont terminées
     * Retourne true si le projet est terminé
     */
    private function updateProjetStatus($projetId)
    {
        // Récupérer toutes les étapes du projet
        $etapes = EtapeProjet::where('id_projet', $projetId)->get();
        $projet = Projet::find($projetId);
        
        if (!$projet) return false;
        
        // Vérifier si toutes les étapes sont terminées
        $allCompleted = true;
        foreach ($etapes as $etape) {
            if ($etape->statut != 'termine' && $etape->progression < 100) {
                $allCompleted = false;
                break;
            }
        }
        
        // Mettre à jour le statut du projet si nécessaire
        if ($allCompleted && $projet->statut != 'termine') {
            $projet->statut = 'termine';
            $projet->save();
            return true;
        } elseif (!$allCompleted && $projet->statut == 'termine') {
            $projet->statut = 'en_cours';
            $projet->save();
        }
        
        return false;
    }
    
    /**
     * Retourne le libellé du statut en français
     */
    private function getStatutLabel($statut)
    {
        switch($statut) {
            case 'a_venir': return 'À venir';
            case 'en_cours': return 'En cours';
            case 'termine': return 'Terminée';
            case 'en_retard': return 'En retard';
            default: return $statut;
        }
    }
}