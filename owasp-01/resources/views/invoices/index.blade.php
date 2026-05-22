@extends('layouts.app')

@section('title', 'Mes factures')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes factures</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $invoices->count() }} facture(s) dans votre espace</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        @if($invoices->isEmpty())
        <div class="px-6 py-16 text-center">
            <p class="text-gray-400 text-sm">Aucune facture disponible.</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N° Facture</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant HT</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Émise le</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Échéance</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($invoices as $invoice)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <a href="{{ route('invoices.show', $invoice->id) }}"
                           class="font-mono text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                            {{ $invoice->number }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $invoice->client_name }}</div>
                        <div class="text-xs text-gray-400">{{ $invoice->client_email }}</div>
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-900">
                        {{ number_format($invoice->amount, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4">
                        @include('invoices._status_badge', ['status' => $invoice->status])
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $invoice->issued_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $invoice->due_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('invoices.show', $invoice->id) }}"
                           class="text-xs font-medium text-gray-500 hover:text-gray-900">
                            Consulter →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
@endsection
