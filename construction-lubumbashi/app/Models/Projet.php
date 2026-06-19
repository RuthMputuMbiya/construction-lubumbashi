<?php
// app/Models/Projet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projet extends Model
{
    use HasFactory;

    protected $table = 'projets';
    
    protected $fillable = [
        'code_projet',
        'nom',
        'description',
        'lieu',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'statut',
        'budget_total',
        'budget_depense',
        'budget_total_fc',
        'budget_depense_fc',
        'budget_valide_fc',
        'budget_total_usd',
        'budget_depense_usd',
        'budget_valide_usd',
        'budget_statut',
        'budget_valide_le',
        'budget_valide_par',
        'id_chef_projet',
        'motif_annulation',
        'annule_le',
        'annule_par',
    ];

    protected $casts = [
        'date_debut_prevue' => 'date',
        'date_fin_prevue' => 'date',
        'date_debut_reelle' => 'date',
        'date_fin_reelle' => 'date',
        'budget_total' => 'decimal:2',
        'budget_depense' => 'decimal:2',
        'budget_total_fc' => 'decimal:2',
        'budget_depense_fc' => 'decimal:2',
        'budget_valide_fc' => 'decimal:2',
        'budget_total_usd' => 'decimal:2',
        'budget_depense_usd' => 'decimal:2',
        'budget_valide_usd' => 'decimal:2',
        'budget_valide_le' => 'datetime',
        'annule_le' => 'datetime',
    ];

    // 🔥 AJOUTER CETTE METHODE ICI 🔥
    /**
     * Vérifie si le projet est en retard
     * Un projet est en retard si la date actuelle dépasse date_fin_prevue ET le projet n'est pas terminé
     */
    public function estEnRetard()
    {
        if ($this->statut === 'termine' || $this->statut === 'annule') {
            return false;
        }
        
        return now()->greaterThan($this->date_fin_prevue);
    }

    // Relations
    public function chefProjet()
    {
        return $this->belongsTo(User::class, 'id_chef_projet');
    }

    public function planifications()
    {
        return $this->hasMany(Planification::class, 'id_projet');
    }

    public function budgetValidateur()
    {
        return $this->belongsTo(User::class, 'budget_valide_par');
    }

    public function annulePar()
    {
        return $this->belongsTo(User::class, 'annule_par');
    }

    // Méthodes budget
    public function getBudgetRestantFc()
    {
        return $this->budget_valide_fc - $this->budget_depense_fc;
    }

    public function getBudgetRestantUsd()
    {
        return $this->budget_valide_usd - $this->budget_depense_usd;
    }

    public function getBudgetConsommationFc()
    {
        if ($this->budget_valide_fc <= 0) return 0;
        return round(($this->budget_depense_fc / $this->budget_valide_fc) * 100, 2);
    }

    public function getBudgetConsommationUsd()
    {
        if ($this->budget_valide_usd <= 0) return 0;
        return round(($this->budget_depense_usd / $this->budget_valide_usd) * 100, 2);
    }

    public function validerBudget($montantFc, $montantUsd, $userId)
    {
        $this->budget_valide_fc = $montantFc;
        $this->budget_valide_usd = $montantUsd;
        $this->budget_total = ($montantFc * 0.00038) + $montantUsd;
        $this->budget_statut = 'valide';
        $this->budget_valide_le = now();
        $this->budget_valide_par = $userId;
        
        if ($this->statut === 'en_attente') {
            $this->statut = 'en_cours';
        }
        
        $this->save();
        return $this;
    }

    public function annulerProjet($motif, $userId)
    {
        $this->statut = 'annule';
        $this->motif_annulation = $motif;
        $this->annule_le = now();
        $this->annule_par = $userId;
        $this->save();
        return $this;
    }

    public function calculerAvancement()
    {
        $etapes = $this->planifications;
        if ($etapes->isEmpty()) return 0;
        return round($etapes->avg('progression'), 2);
    }
}