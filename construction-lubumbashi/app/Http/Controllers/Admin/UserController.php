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
     * CONTRÔLEUR DE GESTION DES UTILISATEURS (ADMIN)
     * 
     * EXPLICATION: Ce contrôleur gère toutes les opérations CRUD sur les utilisateurs
     * Seul l'administrateur peut y accéder (les middlewares sont dans les routes)
     * 
     * 🔥 IMPORTANT: Le constructeur avec middleware a été SUPPRIMÉ car Laravel 13 ne le supporte plus
     * Les middlewares sont désormais gérés directement dans le fichier routes/web.php
     */
    
    // ❌ SUPPRIMEZ COMPLÈTEMENT CE CONSTRUCTEUR :
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'checkRole:admin']);
    // }
    
    /**
     * Affiche la liste de tous les utilisateurs avec filtres
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Requête de base
        $query = User::query();
        
        // Filtre par rôle
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }
        
        // Filtre par recherche (nom ou email)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Pagination (15 par page)
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistiques pour le filtre
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'chef_projet' => User::where('role', 'chef_projet')->count(),
            'chef_chantier' => User::where('role', 'chef_chantier')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }
    
    /**
     * Affiche le formulaire de création d'un utilisateur
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }
    
    /**
     * Enregistre un nouvel utilisateur dans la base de données
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,chef_projet,chef_chantier',
            'telephone' => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.regex' => 'Le nom ne doit contenir que des lettres',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'Format d\'email invalide',
            'email.unique' => 'Cet email est déjà utilisé',
            'role.required' => 'Le rôle est obligatoire',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Générer un mot de passe temporaire sécurisé
        $tempPassword = Str::random(10) . '!' . rand(100, 999);
        
        // Créer l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => $request->role,
            'attributs' => json_encode([
                'telephone' => $request->telephone,
                'entreprise' => $request->entreprise,
                'status' => 'actif',
                'first_login' => true,
                'created_by' => auth()->user()->id,
                'created_by_name' => auth()->user()->nom,
                'created_at_date' => now()->toDateString(),
            ]),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur '{$user->nom}' créé avec succès.");
    }
    
    /**
     * Affiche les détails d'un utilisateur spécifique
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Récupérer les informations supplémentaires des attributs JSON
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        
        // Récupérer les projets créés par l'utilisateur (si chef projet)
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
     * Affiche le formulaire d'édition d'un utilisateur
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher la modification de son propre compte via cette interface
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('warning', 'Utilisez "Mon Profil" pour modifier votre propre compte.');
        }
        
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        
        return view('admin.users.edit', compact('user', 'attributs'));
    }
    
    /**
     * Met à jour les informations d'un utilisateur
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,chef_projet,chef_chantier',
            'telephone' => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
            'status' => 'required|in:actif,suspendu,desactive',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Mettre à jour les attributs JSON
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $attributs['telephone'] = $request->telephone;
        $attributs['entreprise'] = $request->entreprise;
        $attributs['status'] = $request->status;
        $attributs['updated_by'] = auth()->user()->id;
        $attributs['updated_by_name'] = auth()->user()->nom;
        $attributs['updated_at_date'] = now()->toDateTimeString();
        
        // Mise à jour de l'utilisateur
        $user->update([
            'nom' => $request->nom,
            'email' => $request->email,
            'role' => $request->role,
            'attributs' => json_encode($attributs),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur '{$user->nom}' modifié avec succès.");
    }
    
    /**
     * Supprime un utilisateur
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        // Vérifier si l'utilisateur a des projets associés
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
     * Réinitialise le mot de passe d'un utilisateur
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Générer un nouveau mot de passe temporaire
        $tempPassword = Str::random(10) . '!' . rand(100, 999);
        
        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($tempPassword),
        ]);
        
        // Mettre à jour les attributs
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $attributs['first_login'] = true;
        $attributs['last_password_reset'] = now()->toDateTimeString();
        $attributs['password_reset_by'] = auth()->user()->id;
        $user->attributs = json_encode($attributs);
        $user->save();
        
        return redirect()->route('admin.users.index')
            ->with('success', "Mot de passe de '{$user->nom}' réinitialisé avec succès.");
    }
}