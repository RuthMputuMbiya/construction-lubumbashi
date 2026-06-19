<?php
// app/Http/Controllers/Auth/PasswordResetLinkController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Affiche le formulaire de demande de réinitialisation
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoie le lien de réinitialisation par email
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire',
            'email.email' => 'Veuillez entrer une adresse email valide',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}