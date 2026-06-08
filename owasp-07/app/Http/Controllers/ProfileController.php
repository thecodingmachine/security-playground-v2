<?php

declare(strict_types=1);

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

        return view('profile.show', compact('user'));
    }

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

        return redirect()->route('profile.show')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
