<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * ⚠️  VULNÉRABLE : Escalade verticale de privilèges
     * Le champ `role` est inclus dans $request->only() et donc accepté
     * depuis l'entrée utilisateur, sans aucune restriction.
     * Un attaquant peut s'auto-promouvoir en envoyant role=admin dans la requête.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore(Auth::id())],
        ]);

        /** @var User $user */
        $user = Auth::user();

        /** @var array<string, mixed> $data */
        $data = $request->only(['name', 'email', 'role']);
        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
