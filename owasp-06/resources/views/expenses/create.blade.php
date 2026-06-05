@extends('layouts.app')

@section('title', 'Nouvelle note de frais')

@section('content')
<div class="max-w-2xl">

    <div class="mb-6">
        <a href="{{ route('expenses.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Nouvelle note de frais</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-8 py-8">
        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Intitulé de la dépense</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="ex. Déjeuner client Accenture"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                              {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('title')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Montant (€)</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                           step="0.01" min="0.01" required
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                                  {{ $errors->has('amount') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('amount')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de la dépense</label>
                    <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm border-gray-300
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('expense_date')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                <select id="category" name="category" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>Sélectionner…</option>
                    @foreach(['transport', 'repas', 'hébergement', 'fournitures', 'autre'] as $cat)
                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                        {{ ucfirst($cat) }}
                    </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Description <span class="text-gray-400 font-normal">(facultatif)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Contexte, participants, motif professionnel…"
                          class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm resize-none
                                 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Soumettre la note
                </button>
                <a href="{{ route('expenses.index') }}"
                   class="px-5 py-2.5 bg-white text-gray-600 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
