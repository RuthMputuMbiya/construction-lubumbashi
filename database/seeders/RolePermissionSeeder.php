<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * EXPLICATION: Ce seeder crée les rôles et permissions
     * ATTENTION: Les migrations de Spatie doivent être exécutées d'abord
     */
    public function run(): void
    {
        // Réinitialiser le cache des permissions (important!)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->command->info('Création des permissions...');
        
        // === 1. CRÉATION DES PERMISSIONS ===
        
        // Permissions pour la gestion des utilisateurs (ADMIN)
        $userPermissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'reset_user_password',
        ];
        
        // Permissions pour la gestion des projets (CHEF PROJET)
        $projetPermissions = [
            'view_projets',
            'create_projet',
            'edit_projet',
            'delete_projet',
            'cancel_projet',
            'view_projet_details',
        ];
        
        // Permissions pour la planification (CHEF PROJET)
        $planificationPermissions = [
            'view_planification',
            'create_etape',
            'edit_etape',
            'delete_etape',
            'assign_chef_chantier',
        ];
        
        // Permissions pour le suivi (CHEF CHANTIER)
        $suiviPermissions = [
            'view_mes_etapes',
            'update_avancement',
            'view_planning_personnel',
            'justify_retard',
        ];
        
        // Permissions de consultation (TOUS)
        $consultationPermissions = [
            'view_dashboard',
            'view_statistiques',
            'export_rapport',
        ];
        
        // Regrouper toutes les permissions
        $allPermissions = array_merge(
            $userPermissions,
            $projetPermissions,
            $planificationPermissions,
            $suiviPermissions,
            $consultationPermissions
        );
        
        // Créer chaque permission si elle n'existe pas
        foreach ($allPermissions as $permission) {
            try {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => 'web']
                );
                $this->command->info("  ✓ Permission '{$permission}' créée");
            } catch (\Exception $e) {
                $this->command->warn("  ⚠️ Permission '{$permission}' déjà existante");
            }
        }
        
        // === 2. CRÉATION DES RÔLES ===
        $this->command->info('');
        $this->command->info('Création des rôles...');
        
        // RÔLE ADMINISTRATEUR - Toutes les permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);
        $this->command->info("  ✓ Rôle 'admin' créé avec " . count($allPermissions) . " permissions");
        
        // RÔLE CHEF DE PROJET
        $chefProjetRole = Role::firstOrCreate(['name' => 'chef_projet', 'guard_name' => 'web']);
        $chefProjetPermissions = array_merge(
            $projetPermissions,
            $planificationPermissions,
            $consultationPermissions,
            ['view_users'] // Peut voir la liste des chefs chantier
        );
        $chefProjetRole->syncPermissions($chefProjetPermissions);
        $this->command->info("  ✓ Rôle 'chef_projet' créé avec " . count($chefProjetPermissions) . " permissions");
        
        // RÔLE CHEF DE CHANTIER
        $chefChantierRole = Role::firstOrCreate(['name' => 'chef_chantier', 'guard_name' => 'web']);
        $chefChantierPermissions = array_merge(
            $suiviPermissions,
            ['view_projets'],
            ['view_planification']
        );
        $chefChantierRole->syncPermissions($chefChantierPermissions);
        $this->command->info("  ✓ Rôle 'chef_chantier' créé avec " . count($chefChantierPermissions) . " permissions");
        
        // === 3. ASSIGNER LE RÔLE ADMIN À L'UTILISATEUR ADMIN ===
        $this->command->info('');
        $this->command->info('Assignation du rôle admin...');
        
        $adminUser = User::where('email', 'admin@construction-lubumbashi.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
            $this->command->info("  ✓ Rôle 'admin' assigné à l'utilisateur admin");
        } else {
            $this->command->warn("  ⚠️ Utilisateur admin non trouvé");
        }
        
        $this->command->info('');
        $this->command->info('==========================================');
        $this->command->info('✅ SEEDER DES RÔLES ET PERMISSIONS TERMINÉ');
        $this->command->info('==========================================');
    }
}