<?php
// app/Models/DetailDepense.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailDepense extends Model
{
    use HasFactory;

    /**
     * EXPLICATION: Détail granulaire de chaque dépense
     * Permet un suivi précis de toutes les transactions
     */
    
    protected $table = 'details_depenses';
    
    protected $fillable = [
        'idbudget',
        'idprojet',
        'libelle',
        'montant',
        'date_depense',
        'categorie',
        'facture_url',
        'fournisseur',
        'description',
        'statut',
        'valide_par',
        'valide_le',
        'motif_rejet',
    ];
    
    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'date',
        'valide_le' => 'datetime',
    ];
    
    /**
     * RELATIONS
     */
    
    public function budget()
    {
        return $this->belongsTo(Budget::class, 'idbudget', 'Idbudget');
    }
    
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'idprojet', 'idprojet');
    }
    
    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par', 'Idutilisateur');
    }
    
    /**
     * MÉTHODES
     */
    
    public function valider($userId)
    {
        $this->statut = 'valide';
        $this->valide_par = $userId;
        $this->valide_le = now();
        $this->save();
        
        // Mettre à jour le budget principal
        $this->budget->ajouterDepense($this->categorie, $this->montant, null);
    }
    
    public function rejeter($motif)
    {
        $this->statut = 'rejete';
        $this->motif_rejet = $motif;
        $this->save();
    }
}