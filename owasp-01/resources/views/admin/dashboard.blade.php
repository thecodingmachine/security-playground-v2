@extends('layouts.app')

@section('title', 'Administration')

@section('content')
<div class="space-y-6">

    {{-- Warning banner --}}
    <div class="flex items-center gap-3 px-5 py-4 bg-red-50 border border-red-200 rounded-xl">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <p class="text-sm font-semibold text-red-800">Zone Administrateur : Accès restreint</p>
            <p class="text-xs text-red-600 mt-0.5">Cette page contient des données sensibles sur tous les utilisateurs de la plateforme.</p>
        </div>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord administrateur</h1>
        <p class="text-sm text-gray-500 mt-1">Vue globale de la plateforme FacturaPro</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateurs</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['users'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Factures émises</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['invoices'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revenu total (payé)</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['revenue'], 0, ',', ' ') }} €</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">En attente de paiement</p>
            <p class="text-3xl font-bold text-amber-600 mt-2">{{ number_format($stats['pending'], 0, ',', ' ') }} €</p>
        </div>
    </div>

    {{-- Users table --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Tous les utilisateurs</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Factures</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Inscrit le</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $user->id }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-600">{{ $user->invoices_count }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Recent invoices --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Dernières factures (tous utilisateurs)</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentInvoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-xs text-blue-600">{{ $invoice->number }}</td>
                    <td class="px-6 py-3 text-gray-700">{{ $invoice->user->name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $invoice->client_name }}</td>
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
    </div>

</div>
@endsection
