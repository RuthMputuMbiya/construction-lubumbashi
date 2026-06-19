<?php
// app/Http/Controllers/Auth/RegisteredUserController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Affiche la page d'inscription
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des données d'inscription
        $request->validate([
            'nom' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:chef_projet,chef_chantier'],
            'telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'entreprise' => ['nullable', 'string', 'max:255'],
        ], [
            // Messages personnalisés en français
            'nom.required' => 'Le nom est obligatoire',
            'nom.regex' => 'Le nom ne doit contenir que des lettres',
            'email.required' => 'L\'adresse email est obligatoire',
            'email.email' => 'Veuillez entrer une adresse email valide',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
            'role.required' => 'Veuillez sélectionner un rôle',
            'role.in' => 'Rôle non valide',
            'telephone.regex' => 'Format de téléphone non valide',
        ]);

        // Créer l'utilisateur avec les attributs JSON
        $user = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'attributs' => json_encode([
                'telephone' => $request->telephone,
                'entreprise' => $request->entreprise,
                'status' => 'actif',
                'date_inscription' => now()->toDateString(),
                'first_login' => false,
                'email_verified' => false,
                'adresse' => $request->adresse ?? null,
                'ville' => 'Lubumbashi',
                'pays' => 'RDC',
            ]),
        ]);

        // Déclencher l'événement d'inscription
        event(new Registered($user));

        // Connecter automatiquement l'utilisateur
        Auth::login($user);

        // Rediriger selon le rôle
        if ($user->role === 'chef_projet') {
            return redirect()->route('chef-projet.dashboard')
                ->with('success', 'Bienvenue sur ConstrucTrack ! Votre compte a été créé avec succès.');
        }

        return redirect()->route('chef-chantier.dashboard')
            ->with('success', 'Bienvenue sur ConstrucTrack ! Votre compte a été créé avec succès.');
    }
}