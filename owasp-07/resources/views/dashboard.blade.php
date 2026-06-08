@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bonjour, {{ $currentUser->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Bienvenue sur l'intranet CorpHub.</p>
    </div>

    {{-- Announcements --}}
    <div>
        <h2 class="text-base font-semibold text-gray-900 mb-4">Annonces récentes</h2>
        <div class="space-y-4">
            @forelse($announcements as $announcement)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $announcement->category === 'urgent' ? 'bg-red-100 text-red-700' : ($announcement->category === 'rh' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $announcement->category }}
                            </span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</h3>
                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $announcement->content }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-400">Publié par {{ $announcement->author->name }}</span>
                    <span class="text-xs text-gray-400">{{ $announcement->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500">Aucune annonce pour le moment.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
