@extends('layouts.app')

@section('title', 'Journal applicatif')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Journal applicatif</h1>
        <p class="text-sm text-gray-500 mt-1">Affiche les 150 dernières lignes du fichier <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">storage/logs/laravel.log</span>.</p>
    </div>

    <div class="bg-gray-950 rounded-xl p-5 overflow-auto max-h-[70vh]">
        @forelse($lines as $line)
        {{-- ⚠️ VULNÉRABLE : XSS : contenu affiché sans encodage HTML --}}
        <div class="text-xs font-mono text-gray-300 leading-relaxed whitespace-pre-wrap break-all">{!! $line !!}</div>
        @empty
        <p class="text-sm text-gray-500 font-mono">Aucune entrée de log. Effectuez des actions dans l'application pour générer des entrées.</p>
        @endforelse
    </div>

</div>
@endsection
