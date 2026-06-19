<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche la page de connexion
     * 
     * @return View
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion
     * 
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authentifier l'utilisateur
        $request->authenticate();

        // Régénérer la session pour éviter la fixation de session
        $request->session()->regenerate();

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Vérifier si le compte est actif
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $status = $attributs['status'] ?? 'actif';

        if ($status === 'suspendu') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été suspendu. Veuillez contacter l\'administrateur.']);
        }

        if ($status === 'desactive') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.']);
        }

        // Rediriger selon le rôle
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Bienvenue Administrateur !');
            
            case 'chef_projet':
                return redirect()->intended(route('chef-projet.dashboard'))
                    ->with('success', 'Bon retour Chef de Projet !');
            
            case 'chef_chantier':
                return redirect()->intended(route('chef-chantier.dashboard'))
                    ->with('success', 'Bon retour Chef de Chantier !');
            
            default:
                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Bienvenue !');
        }
    }

    /**
     * Déconnexion de l'utilisateur
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}