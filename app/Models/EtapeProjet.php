<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtapeProjet extends Model
{
    protected $table = 'etapes_projets';
    
    protected $fillable = [
        'id_projet',
        'nom_etape',
        'description',
        'ordre',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'progression',
        'statut',
        'commentaires_chantier',
        'justification_retard',
        'id_chef_chantier'
    ];
    
    protected $casts = [
        'date_debut_prevue' => 'date',
        'date_fin_prevue' => 'date',
        'date_debut_reelle' => 'date',
        'date_fin_reelle' => 'date',
        'progression' => 'integer'
    ];
    
    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'id_projet');
    }
    
    public function chefChantier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_chef_chantier');
    }
}