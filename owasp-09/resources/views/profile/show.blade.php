@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="max-w-lg space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>
        <p class="text-sm text-gray-500 mt-1">Mettez à jour vos informations personnelles.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                              {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                @error('name')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                              {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
