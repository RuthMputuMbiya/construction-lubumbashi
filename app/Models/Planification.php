<?php
// app/Models/Planification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Planification extends Model
{
    use HasFactory;

    /**
     * EXPLICATION: Une étape planifiée dans un projet
     * Assignée à un chef de chantier spécifique
     */
    
    protected $table = 'planifications';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nom_etape',
        'description',
        'ordre',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'progression',
        'statut',
        'id_projet',
        'id_chef_chantier',
        'commentaires_chantier',
        'justification_retard',
    ];
    
    protected $casts = [
        'date_debut_prevue' => 'date',
        'date_fin_prevue' => 'date',
        'date_debut_reelle' => 'date',
        'date_fin_reelle' => 'date',
        'progression' => 'integer',
        'ordre' => 'integer',
    ];
    
    /**
     * RELATIONS
     */
    
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet');
    }
    
    public function chefChantier()
    {
        return $this->belongsTo(User::class, 'id_chef_chantier');
    }
    
    /**
     * MÉTHODES DE MISE À JOUR
     */
    
    public function mettreAJourAvancement(int $nouvelleProgression, ?string $commentaires = null): void
    {
        $this->progression = min(100, max(0, $nouvelleProgression));
        
        // Mise à jour du statut
        if ($this->progression >= 100) {
            $this->statut = 'termine';
            $this->date_fin_reelle = now();
        } elseif ($this->progression > 0) {
            $this->statut = 'en_cours';
            if (!$this->date_debut_reelle) {
                $this->date_debut_reelle = now();
            }
        }
        
        // Vérifier si en retard
        if ($this->statut !== 'termine' && now()->greaterThan($this->date_fin_prevue)) {
            $this->statut = 'en_retard';
        }
        
        if ($commentaires) {
            $this->commentaires_chantier = $commentaires;
        }
        
        $this->save();
        
        // Mettre à jour le budget dépensé si étape terminée
        if ($this->progression >= 100) {
            $this->projet->refresh();
            // Logique de mise à jour budget ici
        }
    }
    
    public function justifierRetard(string $justification): void
    {
        $this->justification_retard = $justification;
        $this->statut = 'en_retard';
        $this->save();
    }
    
    public function dureePrevueEnJours(): int
    {
        return $this->date_debut_prevue->diffInDays($this->date_fin_prevue);
    }
    
    public function dureeReelleEnJours(): ?int
    {
        if (!$this->date_debut_reelle) return null;
        $fin = $this->date_fin_reelle ?? now();
        return $this->date_debut_reelle->diffInDays($fin);
    }
}