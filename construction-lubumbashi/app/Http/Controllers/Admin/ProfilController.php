<?php
// app/Http/Controllers/Admin/ProfilController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    /**
     * Afficher mon profil
     */
    public function index()
    {
        $user = Auth::user();
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        
        return view('admin.profil.index', compact('user', 'attributs'));
    }
    
    /**
     * Modifier mon profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $attributs['telephone'] = $request->telephone;
        $attributs['updated_at'] = now()->toDateTimeString();
        
        $user->update([
            'nom' => $request->nom,
            'email' => $request->email,
            'attributs' => json_encode($attributs),
        ]);
        
        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }
    
    /**
     * Changer mon mot de passe
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Mot de passe actuel incorrect.');
        }
        
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        // Mettre à jour les attributs
        $attributs = is_string($user->attributs) ? json_decode($user->attributs, true) : ($user->attributs ?? []);
        $attributs['last_password_change'] = now()->toDateTimeString();
        $user->attributs = json_encode($attributs);
        $user->save();
        
        return redirect()->back()->with('success', 'Mot de passe modifié avec succès. Veuillez vous reconnecter.');
    }
}