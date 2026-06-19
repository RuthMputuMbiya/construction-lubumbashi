<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Affiche la liste de tous les utilisateurs avec filtres
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")  // ✅ Utiliser 'nom'
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'chef_projet' => User::where('role', 'chef_projet')->count(),
            'chef_chantier' => User::where('role', 'chef_chantier')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }
    
    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.users.create');
    }
    
    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,chef_projet,chef_chantier',
            'telephone' => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = User::create([
            'nom' => $request->name,  // ✅ Utiliser 'nom' au lieu de 'name'
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'attributs' => json_encode([
                'telephone' => $request->telephone,
                'entreprise' => $request->entreprise,
                'status' => 'actif',
                'first_login' => false,
                'created_by' => auth()->id() ?? 1,
                'created_by_name' => auth()->user()->nom ?? 'System',  // ✅ Utiliser 'nom'
                'created_at_date' => now()->toDateString(),
            ]),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur '{$user->nom}' créé avec succès.");  // ✅ Utiliser 'nom'
    }
    
    /**
     * Affiche les détails d'un utilisateur
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $projets = [];
        
        if ($user->role == 'chef_projet') {
            $projets = Projet::where('id_chef_projet', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
        
        return view('admin.users.show', compact('user', 'attributs', 'projets'));
    }
    
    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('warning', 'Utilisez "Mon Profil" pour modifier votre propre compte.');
        }
        
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        
        return view('admin.users.edit', compact('user', 'attributs'));
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,chef_projet,chef_chantier',
            'telephone' => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
            'status' => 'required|in:actif,suspendu,desactive',
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $attributs['telephone'] = $request->telephone;
        $attributs['entreprise'] = $request->entreprise;
        $attributs['status'] = $request->status;
        $attributs['updated_by'] = auth()->id() ?? 1;
        $attributs['updated_by_name'] = auth()->user()->nom ?? 'System';  // ✅ Utiliser 'nom'
        $attributs['updated_at_date'] = now()->toDateTimeString();
        
        $updateData = [
            'nom' => $request->name,  // ✅ Utiliser 'nom' au lieu de 'name'
            'email' => $request->email,
            'role' => $request->role,
            'attributs' => json_encode($attributs),
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $user->update($updateData);
        
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur '{$user->nom}' modifié avec succès.");  // ✅ Utiliser 'nom'
    }
    
    /**
     * Supprime un utilisateur
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        if ($user->role === 'chef_projet' && Projet::where('id_chef_projet', $user->id)->count() > 0) {
            $projetsCount = Projet::where('id_chef_projet', $user->id)->count();
            return redirect()->route('admin.users.index')
                ->with('error', "Impossible de supprimer '{$user->nom}'. Cet utilisateur a {$projetsCount} projet(s) associé(s).");
        }
        
        $nom = $user->nom;
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur '{$nom}' supprimé avec succès.");
    }
    
    /**
     * Réinitialise le mot de passe (UNIQUEMENT POUR ADMIN)
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role !== 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'La réinitialisation du mot de passe est réservée aux administrateurs.');
        }
        
        $tempPassword = Str::random(10) . '!' . rand(100, 999);
        
        $user->update([
            'password' => Hash::make($tempPassword),
        ]);
        
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $attributs['first_login'] = true;
        $attributs['last_password_reset'] = now()->toDateTimeString();
        $attributs['password_reset_by'] = auth()->id() ?? 1;
        $user->attributs = json_encode($attributs);
        $user->save();
        
        return redirect()->route('admin.users.index')
            ->with('success', "Mot de passe de '{$user->nom}' réinitialisé avec succès.");
    }
}