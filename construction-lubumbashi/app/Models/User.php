<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * EXPLICATION: Modèle User gère tous les utilisateurs du système
     * 3 rôles: admin, chef_projet, chef_chantier
     * Seul l'admin est créé par seed, les autres s'inscrivent
     */
    
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    // Attributs assignables en masse
    protected $fillable = [
        'nom',
        'email',
        'password',
        'role',
        'attributs',
        'email_verified_at',
    ];

    // Attributs cachés lors de la sérialisation
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts automatiques des types
    protected $casts = [
        'email_verified_at' => 'datetime',
        'attributs' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * RELATIONS
     */
    
    // Un utilisateur (chef projet) peut créer plusieurs projets
    public function projetsCrees()
    {
        return $this->hasMany(Projet::class, 'id_chef_projet');
    }
    
    // Un utilisateur (chef chantier) peut être assigné à plusieurs étapes
    public function etapesAssignees()
    {
        return $this->hasMany(Planification::class, 'id_chef_chantier');
    }
    
    // Un utilisateur peut avoir plusieurs notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_user');
    }
    
    /**
     * MÉTHODES D'AIDE POUR LES RÔLES
     */
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isChefProjet(): bool
    {
        return $this->role === 'chef_projet';
    }
    
    public function isChefChantier(): bool
    {
        return $this->role === 'chef_chantier';
    }
    
    /**
     * MÉTHODES UTILITAIRES
     */
    
    // Vérifier si l'utilisateur peut gérer un projet spécifique
    public function canManageProject(Projet $projet): bool
    {
        if ($this->isAdmin()) return true;
        if ($this->isChefProjet() && $projet->id_chef_projet === $this->id) return true;
        return false;
    }
    
    // Récupérer un attribut spécifique du JSON
    public function getAttribut(string $key, $default = null)
    {
        $attributs = $this->attributs ?? [];
        return $attributs[$key] ?? $default;
    }
    
    // Définir un attribut dans le JSON
    public function setAttribut(string $key, $value): void
    {
        $attributs = $this->attributs ?? [];
        $attributs[$key] = $value;
        $this->attributs = $attributs;
        $this->saveQuietly();
    }
}