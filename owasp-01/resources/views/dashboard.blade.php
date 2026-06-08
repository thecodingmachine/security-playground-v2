@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bonjour, {{ auth()->user()->name }} 👋</h1>
        <p class="text-sm text-gray-500 mt-1">Voici un résumé de votre activité de facturation.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Factures totales</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Payées</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['paid'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">En retard</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['overdue'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revenu encaissé</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats['revenue'], 0, ',', ' ') }} €</p>
        </div>
    </div>

    {{-- Recent invoices --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Factures récentes</h2>
            <a href="{{ route('invoices.index') }}" class="text-xs text-blue-600 hover:underline">Voir tout</a>
        </div>

        @if($recentInvoices->isEmpty())
        <div class="px-6 py-10 text-center text-sm text-gray-400">Aucune facture pour le moment.</div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentInvoices as $invoice)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <a href="{{ route('invoices.show', $invoice->id) }}"
                           class="font-mono text-blue-600 hover:underline">{{ $invoice->number }}</a>
                    </td>
                    <td class="px-6 py-3 text-gray-700">{{ $invoice->client_name }}</td>
                    <td class="px-6 py-3 text-right font-medium text-gray-900">
                        {{ number_format($invoice->amount, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-3">
                        @include('invoices._status_badge', ['status' => $invoice->status])
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
@endsection
