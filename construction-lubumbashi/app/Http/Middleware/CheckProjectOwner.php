<?php
// app/Http/Middleware/CheckProjectOwner.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Projet;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectOwner
{
    /**
     * EXPLICATION: Ce middleware vérifie que le chef de projet est bien le propriétaire
     * Utilisé pour les routes de modification/suppression de projet
     * 
     * L'admin a toujours le droit d'accéder à tous les projets
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();
        
        // L'administrateur a tous les droits
        if ($user->role === 'admin') {
            return $next($request);
        }
        
        // Récupérer l'ID du projet depuis les paramètres de la route
        $projetId = $request->route('id') ?? $request->route('projet');
        
        if (!$projetId) {
            return redirect()->back()->withErrors(['error' => 'Projet non spécifié.']);
        }
        
        $projet = Projet::find($projetId);
        
        if (!$projet) {
            return redirect()->back()->withErrors(['error' => 'Projet introuvable.']);
        }
        
        // Vérifier que le chef de projet est le propriétaire
        if ($user->role === 'chef_projet' && $projet->id_chef_projet === $user->id) {
            return $next($request);
        }
        
        // Si c'est un chef de chantier qui essaie d'accéder, vérifier qu'il participe au projet
        if ($user->role === 'chef_chantier') {
            $participe = $projet->planifications()
                ->where('id_chef_chantier', $user->id)
                ->exists();
            
            if ($participe) {
                return $next($request);
            }
        }
        
        abort(403, 'Vous n\'êtes pas autorisé à accéder à ce projet.');
    }
}