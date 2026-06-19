<?php
// app/Http/Middleware/CheckUserStatus.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * EXPLICATION: Ce middleware vérifie que le compte utilisateur est actif
     * Utile pour bloquer les comptes désactivés ou suspendus
     * 
     * Attribut 'status' dans la colonne 'attributs' JSON
     * status peut être: 'actif', 'suspendu', 'desactive'
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Récupérer le statut depuis les attributs JSON
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : $user->attributs;
        $status = $attributs['status'] ?? 'actif';
        
        // Vérifier si le compte est actif
        if ($status === 'suspendu') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'error' => 'Votre compte a été suspendu. Veuillez contacter l\'administrateur.'
            ]);
        }
        
        if ($status === 'desactive') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'error' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.'
            ]);
        }
        
        // Vérifier si le mot de passe doit être changé (première connexion)
        if ($attributs['first_login'] ?? false) {
            return redirect()->route('password.change')
                ->with('warning', 'Veuillez changer votre mot de passe avant de continuer.');
        }
        
        return $next($request);
    }
}