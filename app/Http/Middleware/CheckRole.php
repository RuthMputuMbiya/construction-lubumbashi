<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // 🔥 ADMIN A TOUS LES ACCÈS 🔥
        if ($user->role == 'admin') {
            return $next($request);
        }
        
        // Vérifier si l'utilisateur a le rôle requis
        if (in_array($user->role, $roles)) {
            return $next($request);
        }
        
        abort(403, 'Accès non autorisé.');
    }
}