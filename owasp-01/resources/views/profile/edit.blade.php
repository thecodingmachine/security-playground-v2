@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="space-y-6 max-w-xl">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>
        <p class="text-sm text-gray-500 mt-1">Modifiez vos informations personnelles.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf

            {{-- Le champ role n'est pas dans le formulaire, mais le serveur l'accepte quand même --}}

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              {{ $errors->has('name') ? 'border-red-400' : '' }}">
                @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              {{ $errors->has('email') ? 'border-red-400' : '' }}">
                @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-1">
                <p class="text-xs text-gray-400 mb-3">
                    Rôle actuel :
                    <span class="font-mono font-semibold {{ $user->role === 'admin' ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $user->role }}
                    </span>
                </p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                               rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Enregistrer les modifications
                </button>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Annuler
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
