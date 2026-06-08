<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * ⚠️  VULNÉRABLE — 3 failles combinées
     * 1. Énumération de comptes : messages d'erreur distincts selon l'existence de l'email.
     * 2. Brute-force : aucune limitation du nombre de tentatives.
     * 3. Fixation de session : la session n'est pas régénérée après l'authentification.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Aucun compte associé à cette adresse.'])
                ->onlyInput('email');
        }

        /** @var string $password */
        $password = $request->input('password', '');

        if (! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Mot de passe incorrect.'])
                ->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        // ⚠️ Session non régénérée — susceptible à la fixation de session

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
