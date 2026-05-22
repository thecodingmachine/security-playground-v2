@extends('layouts.app')

@section('title', $invoice->number)

@section('content')
<div class="space-y-6">

    {{-- Back --}}
    <a href="{{ route('invoices.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Retour aux factures
    </a>

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 font-mono">{{ $invoice->number }}</h1>
                @include('invoices._status_badge', ['status' => $invoice->status])
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Propriétaire : <span class="font-medium text-gray-700">{{ $invoice->user->name }}</span>
                ({{ $invoice->user->email }})
            </p>
        </div>
        <div class="text-right">
            <p class="text-3xl font-bold text-gray-900">{{ number_format($invoice->amount, 2, ',', ' ') }} €</p>
            <p class="text-xs text-gray-400 mt-1">montant HT</p>
        </div>
    </div>

    {{-- Details grid --}}
    <div class="grid grid-cols-2 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Client</h2>
            <div class="space-y-2">
                <p class="font-semibold text-gray-900">{{ $invoice->client_name }}</p>
                <p class="text-sm text-gray-500">{{ $invoice->client_email }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Dates</h2>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Date d'émission</span>
                    <span class="font-medium text-gray-900">{{ $invoice->issued_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Échéance</span>
                    <span class="font-medium {{ $invoice->status === 'overdue' ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $invoice->due_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($invoice->notes)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Notes</h2>
        <p class="text-sm text-gray-700">{{ $invoice->notes }}</p>
    </div>
    @endif

    {{-- Documents --}}
    @if($invoice->documents->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Documents attachés</h2>
        <div class="space-y-2">
            @foreach($invoice->documents as $document)
            <div class="flex items-center justify-between py-2.5 px-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $document->original_name }}</p>
                        <p class="text-xs text-gray-400">{{ number_format($document->size / 1024, 0) }} KB</p>
                    </div>
                </div>
                <a href="{{ route('documents.download', ['file' => $document->filename]) }}"
                   class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline">
                    Télécharger
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Danger zone --}}
    <div class="bg-white rounded-xl border border-red-200 p-6">
        <h2 class="text-xs font-semibold text-red-500 uppercase tracking-wider mb-1">Zone de danger</h2>
        <p class="text-sm text-gray-500 mb-4">La suppression d'une facture est définitive et ne peut pas être annulée.</p>
        <form method="POST" action="{{ route('invoices.destroy', $invoice->id) }}"
              onsubmit="return confirm('Supprimer définitivement cette facture ?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                Supprimer la facture
            </button>
        </form>
    </div>

</div>
@endsection
