@extends('layouts.app')

@section('title', 'Mes notes de frais')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes notes de frais</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $expenses->count() }} note(s)</p>
        </div>
        <a href="{{ route('expenses.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle note
        </a>
    </div>

    @if($expenses->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-12 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm text-gray-500">Aucune note de frais. Créez-en une !</p>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Titre</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Catégorie</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Montant</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($expenses as $expense)
                @php
                    $statusClasses = match($expense->status) {
                        'approuvée' => 'bg-green-100 text-green-700',
                        'rejetée'   => 'bg-red-100 text-red-700',
                        default     => 'bg-amber-100 text-amber-700',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $expense->title }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-gray-500 capitalize">{{ $expense->category }}</td>
                    <td class="px-6 py-4 text-gray-900 font-semibold text-right">{{ number_format((float) $expense->amount, 2, ',', ' ') }} €</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                            {{ $expense->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('expenses.show', $expense) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Voir</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
