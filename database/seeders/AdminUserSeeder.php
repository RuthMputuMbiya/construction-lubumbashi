<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * EXPLICATION: Ce seeder crée UNIQUEMENT le compte administrateur
     * Les chefs de projet et chefs de chantier doivent s'inscrire eux-mêmes
     * via le formulaire d'inscription publique
     */
    public function run(): void
    {
        // 🔥 CORRECTION: Utilisez le même email partout
        $adminEmail = 'jenovic@gmail.com';  // ← Email réel de l'admin
        
        // Vérifier si l'administrateur existe déjà
        $adminExists = User::where('email', $adminEmail)->exists();
        
        if (!$adminExists) {
            User::create([
                'nom' => 'Administrateur Principal',
                'email' => $adminEmail,  // ← Utilise jenovic@gmail.com
                'email_verified_at' => now(),
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'attributs' => json_encode([
                    'telephone' => '+243 813 833 801',
                    'poste' => 'Directeur des Systèmes d\'Information',
                    'bureau' => 'Siège Social - Lubumbashi',
                    'date_embauche' => '2026-01-01',
                    'matricule' => 'ADM-001',
                    'status' => 'actif'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info('==========================================');
            $this->command->info('✅ COMPTE ADMINISTRATEUR CRÉÉ AVEC SUCCÈS');
            $this->command->info('==========================================');
            $this->command->info('📧 Email    : jenovic@gmail.com');  // ← Email correct
            $this->command->info('🔑 Password : Admin123!');
            $this->command->info('==========================================');
        } else {
            // 🔥 Si l'admin existe, mettons à jour son mot de passe
            $admin = User::where('email', $adminEmail)->first();
            $admin->password = Hash::make('Admin123!');
            $admin->save();
            
            $this->command->info('==========================================');
            $this->command->info('🔄 COMPTE ADMINISTRATEUR MIS À JOUR');
            $this->command->info('==========================================');
            $this->command->info('📧 Email    : jenovic@gmail.com');
            $this->command->info('🔑 Password : Admin123!');
            $this->command->info('==========================================');
        }
    }
}