<?php
// database/migrations/0001_01_01_000000_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EXPLICATION: Table des utilisateurs avec 3 rôles
     * - admin: administrateur du système
     * - chef_projet: gère les projets et planifications
     * - chef_chantier: met à jour l'avancement
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Clé primaire 'id'
            $table->string('nom');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'chef_projet', 'chef_chantier'])->default('chef_chantier');
            $table->json('attributs')->nullable(); // Stocke telephone, status, etc.
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};