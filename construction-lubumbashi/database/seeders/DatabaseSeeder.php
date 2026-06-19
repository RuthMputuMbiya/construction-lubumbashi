<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * EXPLICATION: Seeder principal
     * Ordre d'exécution IMPORTANT:
     * 1. AdminUserSeeder (créer l'admin)
     * 2. Exécuter les migrations Spatie (faites manuellement)
     * 3. RolePermissionSeeder (créer rôles et permissions)
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 DÉMARRAGE DES SEEDERS...');
        $this->command->info('');
        
        // Étape 1: Créer le compte administrateur
        $this->call(AdminUserSeeder::class);
        
        // Étape 2: Vérifier que les tables Spatie existent
        if (!\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
            $this->command->error('❌ Les tables de permissions n\'existent pas!');
            $this->command->info('▶️ Exécutez d\'abord: php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permissions.migrations"');
            $this->command->info('▶️ Puis: php artisan migrate');
            return;
        }
        
        // Étape 3: Créer les rôles et permissions
        $this->call(RolePermissionSeeder::class);
        
        $this->command->info('');
        $this->command->info('🎉 TOUS LES SEEDERS ONT ÉTÉ EXÉCUTÉS AVEC SUCCÈS !');
        $this->command->info('');
    }
}