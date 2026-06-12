<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * ⚠️  VULNÉRABLE : Injection de logs
     * Le nom de l'utilisateur est interpolé directement dans le message de log.
     * Un attaquant peut y glisser des retours à la ligne (\n) pour fabriquer
     * de fausses entrées de log (ex. : une fausse connexion admin réussie).
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        /** @var array<string, mixed> $data */
        $data = $request->only(['name', 'email']);
        $user->update($data);

        /** @var string $name */
        $name = $data['name'];

        // ⚠️ Interpolation directe : si $name contient \n, l'attaquant forge des lignes de log
        Log::info("profile_updated: {$name}");

        return redirect()->route('profile.show')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
