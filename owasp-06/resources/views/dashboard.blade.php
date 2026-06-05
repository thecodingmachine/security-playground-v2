@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-8">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
        <p class="text-sm text-gray-500 mt-1">Bienvenue, {{ auth()->user()->name }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Notes soumises</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalCount }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Montant remboursé</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format((float) $totalAmount, 2, ',', ' ') }} €</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">En attente</p>
            <p class="text-3xl font-bold text-amber-600">{{ $pendingCount }}</p>
        </div>
    </div>

    {{-- Recent expenses --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Notes récentes</h2>
            <a href="{{ route('expenses.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Voir tout</a>
        </div>
        @if($recentExpenses->isEmpty())
        <div class="px-6 py-8 text-center">
            <p class="text-sm text-gray-400">Aucune note de frais pour l'instant.</p>
            <a href="{{ route('expenses.create') }}"
               class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Créer ma première note
            </a>
        </div>
        @else
        <div class="divide-y divide-gray-100">
            @foreach($recentExpenses as $expense)
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $expense->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $expense->expense_date->format('d/m/Y') }} · {{ $expense->category }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-gray-900">{{ number_format((float) $expense->amount, 2, ',', ' ') }} €</span>
                    @php
                        $statusClasses = match($expense->status) {
                            'approuvée' => 'bg-green-100 text-green-700',
                            'rejetée'   => 'bg-red-100 text-red-700',
                            default     => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                        {{ $expense->status }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
