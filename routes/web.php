<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
// ============================================
// IMPORTS DES CONTRÔLEURS
// ============================================

// Contrôleurs Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProjetController as AdminProjetController;
use App\Http\Controllers\Admin\StatistiqueController;
use App\Http\Controllers\Admin\ProfilController;


// Contrôleurs Chef Projet
use App\Http\Controllers\ChefProjet\DashboardController as ChefProjetDashboardController;
use App\Http\Controllers\ChefProjet\ProjetController;
use App\Http\Controllers\ChefProjet\PlanificationController;
use App\Http\Controllers\ChefProjet\SuiviController as ChefProjetSuiviController;

// Contrôleurs Chef Chantier
use App\Http\Controllers\ChefChantier\DashboardController as ChefChantierDashboardController;
use App\Http\Controllers\ChefChantier\EtapeController;
use App\Http\Controllers\ChefChantier\PlanningController;

// ============================================
// ROUTES PUBLIQUES
// ============================================

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Routes d'authentification (login, register, etc.)
require __DIR__ . '/auth.php';

// ============================================
// ROUTES PROTÉGÉES (Nécessitent connexion)
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal (redirection selon rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestion du profil (pour tous les utilisateurs connectés)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================
// ROUTES ADMINISTRATEUR
// Prefix: /admin
// Middleware: auth, verified, role:admin
// ============================================

Route::middleware(['auth', 'verified', 'checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // GESTION DES UTILISATEURS (CRUD complet)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');


    Route::get('/projets', [App\Http\Controllers\Admin\ProjetController::class, 'index'])->name('projets.index');
    Route::get('/projets/{id}', [App\Http\Controllers\Admin\ProjetController::class, 'show'])->name('projets.show');
    Route::get('/projets/create', [App\Http\Controllers\Admin\ProjetController::class, 'create'])->name('projets.create');
    Route::post('/projets', [App\Http\Controllers\Admin\ProjetController::class, 'store'])->name('projets.store');
    Route::get('/projets/{id}/edit', [App\Http\Controllers\Admin\ProjetController::class, 'edit'])->name('projets.edit');
    Route::put('/projets/{id}', [App\Http\Controllers\Admin\ProjetController::class, 'update'])->name('projets.update');
    Route::post('/projets/{id}/cancel', [App\Http\Controllers\Admin\ProjetController::class, 'cancel'])->name('projets.cancel');
    Route::delete('/projets/{id}', [App\Http\Controllers\Admin\ProjetController::class, 'destroy'])->name('projets.destroy');


    // PROFIL ADMIN (réinitialisation mot de passe)
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/change-password', [ProfilController::class, 'changePassword'])->name('profil.change-password');
});

/// ROUTES CHEF DE PROJET
Route::middleware(['auth', 'verified', 'checkRole:chef_projet'])->prefix('chef-projet')->name('chef-projet.')->group(function () {

    Route::get('/dashboard', [ChefProjetDashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // PLANIFICATION (AVANT CRÉATION DU PROJET)
    // ==========================================
    Route::prefix('planification')->name('planification.')->group(function () {
        Route::get('/', [PlanificationController::class, 'index'])->name('index');
        Route::get('/create', [PlanificationController::class, 'create'])->name('create');
        Route::post('/', [PlanificationController::class, 'store'])->name('store');

        // Gestion des étapes
        Route::get('/{idPlanification}/etapes', [PlanificationController::class, 'etapes'])->name('etapes');
        Route::post('/{idPlanification}/etapes', [PlanificationController::class, 'ajouterEtape'])->name('ajouter-etape');
        Route::delete('/{idPlanification}/etapes/{idEtape}', [PlanificationController::class, 'supprimerEtape'])->name('supprimer-etape');
        Route::delete('/{idPlanification}/etapes/{idEtape}/retirer-chef', [PlanificationController::class, 'retirerChefChantier'])->name('retirer-chef');

        // Modifier/Assigner le chef
        Route::put('/{idPlanification}/etapes/{idEtape}/modifier-chef', [PlanificationController::class, 'modifierChef'])->name('modifier-chef');
        Route::put('/{idPlanification}/etapes/{idEtape}/assigner-chef', [PlanificationController::class, 'assignerChef'])->name('assigner-chef');

        // Modifier une étape
        Route::get('/{idPlanification}/etapes/{idEtape}/edit', [PlanificationController::class, 'editEtape'])->name('edit-etape');
        Route::put('/{idPlanification}/etapes/{idEtape}', [PlanificationController::class, 'updateEtape'])->name('update-etape');

        // Transformer en projet
        Route::post('/{idPlanification}/transformer', [PlanificationController::class, 'transformerEnProjet'])->name('transformer');

        // Détails
        Route::get('/{idPlanification}', [PlanificationController::class, 'show'])->name('show');
    });

     // 1. ROUTES PERSONNALISÉES (DOIVENT être AVANT resource)
    Route::get('/projets/{projet}/validate-budget', [ProjetController::class, 'validateBudgetPage'])->name('projets.validate-budget-page');
    Route::post('/projets/{projet}/validate-budget', [ProjetController::class, 'validateBudget'])->name('projets.validate-budget');
    Route::post('/projets/{projet}/cancel', [ProjetController::class, 'cancel'])->name('projets.cancel');

    // 2. ROUTE RESOURCE (DOIT être APRÈS les routes personnalisées)
    Route::resource('projets', ProjetController::class);

    // ==========================================
    // SUIVI AVANCEMENT
    // ==========================================
    Route::prefix('suivi')->name('suivi.')->group(function () {
        Route::get('/', [ChefProjetSuiviController::class, 'index'])->name('index');
        Route::get('/{id}/chart', [ChefProjetSuiviController::class, 'chart'])->name('chart');
        Route::get('/{id}/rapport', [ChefProjetSuiviController::class, 'rapport'])->name('rapport');
        Route::get('/{id}/export', [ChefProjetSuiviController::class, 'export'])->name('export');
   
        });
});

// ============================================
// ROUTES CHEF DE CHANTIER
// Prefix: /chef-chantier
// Middleware: auth, verified, role:chef_chantier
// ============================================

Route::middleware(['auth', 'verified', 'checkRole:chef_chantier'])->prefix('chef-chantier')->name('chef-chantier.')->group(function () {

    // Dashboard Chef Chantier
    Route::get('/dashboard', [ChefChantierDashboardController::class, 'index'])->name('dashboard');

    // Gestion des étapes (mise à jour avancement)
    Route::get('/etapes', [EtapeController::class, 'index'])->name('etapes.index');
    Route::get('/etapes/{id}', [EtapeController::class, 'show'])->name('etapes.show');
    Route::get('/etapes/{id}/edit', [EtapeController::class, 'edit'])->name('etapes.edit');
    Route::put('/etapes/{id}', [EtapeController::class, 'update'])->name('etapes.update');

    Route::put('/etapes/{id}/terminer', [EtapeController::class, 'terminer'])->name('etapes.terminer');

    // Planning personnel
    Route::get('/planning', [PlanningController::class, 'index'])->name('planning.index');
    Route::get('/planning/calendar', [PlanningController::class, 'calendar'])->name('planning.calendar');
    Route::get('/planning/export', [PlanningController::class, 'export'])->name('planning.export');
});

// ============================================
// NOTE: Le contrôleur DashboardController principal doit exister
// Il redirige vers le dashboard approprié selon le rôle
// ============================================









require __DIR__ . '/auth.php';
