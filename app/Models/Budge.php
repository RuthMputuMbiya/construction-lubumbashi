<?php
// app/Models/Budget.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    use HasFactory;

    /**
     * EXPLICATION: Modèle Budget - Gère les finances des projets
     * Permet de:
     * - Suivre les dépenses par catégorie
     * - Calculer automatiquement le reste à dépenser
     * - Détecter les dépassements
     * - Gérer les révisions budgétaires
     * - Historiser toutes les modifications
     */
    
    protected $table = 'budgets';
    protected $primaryKey = 'Idbudget';
    
    // Attributs assignables en masse
    protected $fillable = [
        'montanttotal',
        'depenses',
        'depenses_materiaux',
        'depenses_main_oeuvre',
        'depenses_equipement',
        'depenses_transport',
        'depenses_administratives',
        'depenses_imprevu',
        'engagements',
        'reel_vs_prevue',
        'taux_consommation',
        'echeancier_paiements',
        'observations_budget',
        'statut_budget',
        'nombre_revisions',
        'historique_revisions',
        'valide_le',
        'valide_par',
        'idprojet',
    ];
    
    // Casts automatiques
    protected $casts = [
        'montanttotal' => 'decimal:2',
        'depenses' => 'decimal:2',
        'reste' => 'decimal:2',
        'depenses_materiaux' => 'decimal:2',
        'depenses_main_oeuvre' => 'decimal:2',
        'depenses_equipement' => 'decimal:2',
        'depenses_transport' => 'decimal:2',
        'depenses_administratives' => 'decimal:2',
        'depenses_imprevu' => 'decimal:2',
        'engagements' => 'decimal:2',
        'reel_vs_prevue' => 'decimal:2',
        'taux_consommation' => 'decimal:2',
        'echeancier_paiements' => 'array',
        'historique_revisions' => 'array',
        'valide_le' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Valeurs par défaut
    protected $attributes = [
        'depenses' => 0,
        'depenses_materiaux' => 0,
        'depenses_main_oeuvre' => 0,
        'depenses_equipement' => 0,
        'depenses_transport' => 0,
        'depenses_administratives' => 0,
        'depenses_imprevu' => 0,
        'engagements' => 0,
        'statut_budget' => 'normal',
        'nombre_revisions' => 0,
    ];
    
    /**
     * Boot du modèle
     * Calcul automatique du reste avant sauvegarde
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($budget) {
            // Calculer la somme des dépenses par catégorie
            $budget->depenses = $budget->depenses_materiaux
                              + $budget->depenses_main_oeuvre
                              + $budget->depenses_equipement
                              + $budget->depenses_transport
                              + $budget->depenses_administratives
                              + $budget->depenses_imprevu;
            
            // Calculer le montant restant (champ virtuel)
            $budget->reste = $budget->montanttotal - $budget->depenses;
            
            // Calculer le taux de consommation
            if ($budget->montanttotal > 0) {
                $budget->taux_consommation = round(($budget->depenses / $budget->montanttotal) * 100, 2);
            } else {
                $budget->taux_consommation = 0;
            }
            
            // Déterminer le statut du budget
            if ($budget->taux_consommation >= 100) {
                $budget->statut_budget = 'critique';
            } elseif ($budget->taux_consommation >= 80) {
                $budget->statut_budget = 'alerte';
            } else {
                $budget->statut_budget = 'normal';
            }
        });
    }
    
    /**
     * RELATIONS
     */
    
    /**
     * Relation: Un budget appartient à un projet
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'idprojet', 'idprojet');
    }
    
    /**
     * Relation: Un budget a été validé par un utilisateur
     */
    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par', 'Idutilisateur');
    }
    
    /**
     * Relation: Un budget peut avoir plusieurs détails de dépenses
     */
    public function detailsDepenses()
    {
        return $this->hasMany(DetailDepense::class, 'idbudget', 'Idbudget');
    }
    
    /**
     * MÉTHODES DE CALCUL
     */
    
    /**
     * Calcule et retourne les informations budgétaires complètes
     * 
     * @return array
     */
    public function calculerBudget()
    {
        $total = $this->montanttotal;
        $depenses = $this->depenses;
        $reste = $total - $depenses;
        $taux = $total > 0 ? round(($depenses / $total) * 100, 2) : 0;
        
        $budgetPrevisionnelRestant = $reste - $this->engagements;
        
        return [
            'total' => $total,
            'depenses' => $depenses,
            'reste' => $reste,
            'taux_consommation' => $taux,
            'engagements' => $this->engagements,
            'disponible_reel' => $budgetPrevisionnelRestant,
            'statut' => $this->statut_budget,
            'par_categorie' => [
                'materiaux' => $this->depenses_materiaux,
                'main_oeuvre' => $this->depenses_main_oeuvre,
                'equipement' => $this->depenses_equipement,
                'transport' => $this->depenses_transport,
                'administratif' => $this->depenses_administratives,
                'imprevu' => $this->depenses_imprevu,
            ]
        ];
    }
    
    /**
     * Ajoute une dépense dans une catégorie spécifique
     * 
     * @param string $categorie La catégorie (materiaux, main_oeuvre, etc.)
     * @param float $montant Le montant à ajouter
     * @param string $description Description optionnelle
     * @return array
     */
    public function ajouterDepense($categorie, $montant, $description = null)
    {
        $categoriesValides = [
            'materiaux', 'main_oeuvre', 'equipement', 
            'transport', 'administratives', 'imprevu'
        ];
        
        if (!in_array($categorie, $categoriesValides)) {
            throw new \InvalidArgumentException("Catégorie invalide: $categorie");
        }
        
        $champ = "depenses_{$categorie}";
        $this->$champ += $montant;
        
        // Sauvegarder (le boot recalcule automatiquement)
        $this->save();
        
        // Enregistrer dans les détails si description fournie
        if ($description) {
            DetailDepense::create([
                'idbudget' => $this->Idbudget,
                'idprojet' => $this->idprojet,
                'libelle' => $description,
                'montant' => $montant,
                'date_depense' => now(),
                'categorie' => $categorie,
                'statut' => 'en_attente',
            ]);
        }
        
        return $this->calculerBudget();
    }
    
    /**
     * Valide le budget (signature du chef de projet)
     * 
     * @return bool
     */
    public function validerBudget()
    {
        $this->valide_le = now();
        $this->valide_par = auth()->id();
        
        return $this->save();
    }
    
    /**
     * Révise le budget total (augmentation/diminution)
     * Sauvegarde l'historique des modifications
     * 
     * @param float $nouveauMontant Nouveau budget total
     * @param string $motif Raison de la révision
     * @return array
     */
    public function reviserBudget($nouveauMontant, $motif)
    {
        $ancienMontant = $this->montanttotal;
        
        // Enregistrer dans l'historique
        $historique = $this->historique_revisions ?? [];
        $historique[] = [
            'date' => now()->toDateTimeString(),
            'ancien_montant' => $ancienMontant,
            'nouveau_montant' => $nouveauMontant,
            'motif' => $motif,
            'modifie_par' => auth()->user()->nom,
            'modifie_par_id' => auth()->id(),
        ];
        
        $this->montanttotal = $nouveauMontant;
        $this->historique_revisions = $historique;
        $this->nombre_revisions += 1;
        $this->save();
        
        return [
            'ancien' => $ancienMontant,
            'nouveau' => $nouveauMontant,
            'difference' => $nouveauMontant - $ancienMontant,
            'historique' => $historique
        ];
    }
    
    /**
     * Vérifie si le budget est dépassé
     * 
     * @return bool
     */
    public function estDepasse()
    {
        return $this->depenses > $this->montanttotal;
    }
    
    /**
     * Calcule l'écart entre le prévisionnel et le réel
     * 
     * @return float
     */
    public function getEcart()
    {
        return $this->montanttotal - $this->depenses;
    }
    
    /**
     * Génère un rapport budgétaire complet
     * 
     * @return array
     */
    public function genererRapport()
    {
        $info = $this->calculerBudget();
        
        // Ajouter les engagements
        $info['engagements'] = $this->engagements;
        
        // Ajouter les échéanciers de paiement
        $info['echeancier'] = $this->echeancier_paiements;
        
        // Ajouter les observations
        $info['observations'] = $this->observations_budget;
        
        // Ajouter la date de validation
        $info['valide_le'] = $this->valide_le;
        $info['valide_par'] = $this->validateur ? $this->validateur->nom : null;
        
        // Ajouter l'historique des révisions
        $info['historique_revisions'] = $this->historique_revisions;
        
        return $info;
    }
    
    /**
     * MÉTHODES STATIQUES
     */
    
    /**
     * Récupère les budgets en alerte (dépassement ou proche dépassement)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBudgetsEnAlerte()
    {
        return self::where('statut_budget', 'alerte')
                   ->orWhere('statut_budget', 'critique')
                   ->with('projet')
                   ->get();
    }
    
    /**
     * Calcule les statistiques budgétaires globales
     * 
     * @return array
     */
    public static function getStatistiquesGlobales()
    {
        $stats = [
            'total_budgets' => self::sum('montanttotal'),
            'total_depenses' => self::sum('depenses'),
            'total_restant' => self::sum(DB::raw('montanttotal - depenses')),
            'budgets_alerte' => self::where('statut_budget', 'alerte')->count(),
            'budgets_critique' => self::where('statut_budget', 'critique')->count(),
        ];
        
        $stats['taux_global'] = $stats['total_budgets'] > 0 
            ? round(($stats['total_depenses'] / $stats['total_budgets']) * 100, 2)
            : 0;
        
        return $stats;
    }
}