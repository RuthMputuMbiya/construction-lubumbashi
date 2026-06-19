<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'chef_projet') {
            return redirect()->route('chef-projet.dashboard');
        } elseif ($user->role == 'chef_chantier') {
            return redirect()->route('chef-chantier.dashboard');
        }

        return redirect()->route('home');
    }
}
