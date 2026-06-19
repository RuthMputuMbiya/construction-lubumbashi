<?php
// app/Models/Suivi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Suivi extends Model
{
    use HasFactory;

    /**
     * EXPLICATION: Modèle Suivi - Gère l'historique d'avancement des projets
     * Permet de:
     * - Enregistrer une mise à jour de progression
     * - Calculer la tendance d'avancement
     * - Générer des graphiques d'évolution
     * - Détecter les dérives (retards, surcoûts)
     */
    
    protected $table = 'suivis';
    protected $primaryKey = 'Idsuivi';
    
    // Attributs assignables en masse
    protected $fillable = [
        'datesuivi',
        'progression',
        'observation',
        'budget_consomme',
        'duree_ecoulee',
        'duree_restante',
        'difficultes',
        'actions_correctives',
        'medias',
        'type_mise_a_jour',
        'idprojet',
        'cree_par',
    ];
    
    // Casts automatiques des types
    protected $casts = [
        'datesuivi' => 'date',
        'progression' => 'integer',
        'budget_consomme' => 'decimal:2',
        'duree_ecoulee' => 'integer',
        'duree_restante' => 'integer',
        'medias' => 'array',          // Convertit JSON en tableau PHP
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Valeurs par défaut
    protected $attributes = [
        'progression' => 0,
        'type_mise_a_jour' => 'hebdomadaire',
    ];
    
    /**
     * RELATIONS
     */
    
    /**
     * Relation: Un suivi appartient à un projet
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'idprojet', 'idprojet');
    }
    
    /**
     * Relation: Un suivi a été créé par un utilisateur
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par', 'Idutilisateur');
    }
    
    /**
     * MÉTHODES D'ENREGISTREMENT
     */
    
    /**
     * Enregistre un nouveau suivi pour un projet
     * 
     * @param int $projetId ID du projet
     * @param array $data Données du suivi
     * @return Suivi
     */
    public function enregistrerSuivi($projetId, array $data)
    {
        $data['idprojet'] = $projetId;
        $data['cree_par'] = auth()->id();
        $data['datesuivi'] = $data['datesuivi'] ?? now()->toDateString();
        
        // Vérifier si un suivi existe déjà pour cette date
        $existant = $this->where('idprojet', $projetId)
                         ->where('datesuivi', $data['datesuivi'])
                         ->first();
        
        if ($existant) {
            // Mettre à jour l'existant
            $existant->update($data);
            return $existant;
        }
        
        // Créer un nouveau suivi
        return $this->create($data);
    }
    
    /**
     * Met à jour l'avancement d'un projet
     * Calcule automatiquement les métriques associées
     * 
     * @param int $id ID du suivi
     * @param int $progression Nouveau pourcentage
     * @return Suivi
     */
    public function mettreAJourAvancement($id, $progression)
    {
        $suivi = $this->findOrFail($id);
        $suivi->progression = $progression;
        
        // Recalculer la durée restante estimée
        $projet = $suivi->projet;
        if ($projet && $progression > 0) {
            $dureeTotalePrevue = $projet->date_debut_prevue->diffInDays($projet->date_fin_prevue);
            $dureeEcoulee = $projet->date_debut_prevue->diffInDays(now());
            $dureeRestanteEstimee = ($dureeTotalePrevue - $dureeEcoulee) * (100 - $progression) / max(1, $progression);
            
            $suivi->duree_ecoulee = $dureeEcoulee;
            $suivi->duree_restante = max(0, (int)$dureeRestanteEstimee);
        }
        
        $suivi->save();
        
        // Mettre à jour la progression dans le projet
        if ($projet) {
            $projet->touch(); // Met à jour updated_at
        }
        
        return $suivi;
    }
    
    /**
     * MÉTHODES DE CALCUL ET ANALYSE
     */
    
    /**
     * Calcule la vitesse d'avancement (progression par jour)
     * 
     * @return float
     */
    public function getVitesseAvancement()
    {
        if (!$this->projet || $this->progression == 0) {
            return 0;
        }
        
        $dateDebut = $this->projet->date_debut_reelle ?? $this->projet->date_debut_prevue;
        $jours = $dateDebut->diffInDays($this->datesuivi);
        
        if ($jours <= 0) return 0;
        
        return round($this->progression / $jours, 2);
    }
    
    /**
     * Prédit la date de fin estimée basée sur la vitesse actuelle
     * 
     * @return \Carbon\Carbon|null
     */
    public function getDateFinEstimee()
    {
        $vitesse = $this->getVitesseAvancement();
        if ($vitesse <= 0) return null;
        
        $progressionRestante = 100 - $this->progression;
        $joursRestants = ceil($progressionRestante / $vitesse);
        
        return $this->datesuivi->copy()->addDays($joursRestants);
    }
    
    /**
     * Vérifie si le projet est en avance ou en retard
     * 
     * @return string 'avance', 'retard', 'normal'
     */
    public function getStatutDelai()
    {
        if (!$this->projet) return 'normal';
        
        $dateFinEstimee = $this->getDateFinEstimee();
        if (!$dateFinEstimee) return 'normal';
        
        $dateFinPrevue = $this->projet->date_fin_prevue;
        
        if ($dateFinEstimee->lt($dateFinPrevue)) {
            return 'avance';
        } elseif ($dateFinEstimee->gt($dateFinPrevue)) {
            return 'retard';
        }
        
        return 'normal';
    }
    
    /**
     * Calcule la tendance (évolution de progression sur les 7 derniers jours)
     * 
     * @return float
     */
    public static function getTendance($projetId, $jours = 7)
    {
        $suivis = self::where('idprojet', $projetId)
                      ->orderBy('datesuivi', 'desc')
                      ->limit(2)
                      ->get();
        
        if ($suivis->count() < 2) return 0;
        
        $dernier = $suivis[0];
        $precedent = $suivis[1];
        
        return $dernier->progression - $precedent->progression;
    }
    
    /**
     * MÉTHODES STATIQUES
     */
    
    /**
     * Récupère l'historique complet d'un projet avec analyse
     * 
     * @param int $projetId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getHistoriqueComplet($projetId)
    {
        $suivis = self::where('idprojet', $projetId)
                      ->with('createur')
                      ->orderBy('datesuivi', 'asc')
                      ->get();
        
        // Ajouter des métriques calculées à chaque suivi
        foreach ($suivis as $suivi) {
            $suivi->vitesse = $suivi->getVitesseAvancement();
            $suivi->statut_delai = $suivi->getStatutDelai();
        }
        
        return $suivis;
    }
    
    /**
     * Génère les données pour un graphique d'avancement
     * 
     * @param int $projetId
     * @return array
     */
    public static function getDataForChart($projetId)
    {
        $suivis = self::where('idprojet', $projetId)
                      ->orderBy('datesuivi', 'asc')
                      ->get(['datesuivi', 'progression']);
        
        return [
            'labels' => $suivis->pluck('datesuivi')->map(function($date) {
                return $date->format('d/m/Y');
            })->toArray(),
            'data' => $suivis->pluck('progression')->toArray(),
        ];
    }
}