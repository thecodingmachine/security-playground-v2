@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-8">

    {{-- Header + balance --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bonjour, {{ $currentUser->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Bienvenue sur VaultBank.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-4 text-right">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Solde disponible</p>
            <p class="text-2xl font-bold text-emerald-700">{{ number_format($currentUser->balance, 2, ',', ' ') }} €</p>
        </div>
    </div>

    {{-- Recent transfers --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Dernières opérations</h2>
            <a href="{{ route('transfers.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Voir tout</a>
        </div>

        @if($recentTransfers->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
            <p class="text-sm text-gray-500">Aucune opération pour le moment.</p>
            <a href="{{ route('transfers.index') }}"
               class="mt-3 inline-block px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                Effectuer un virement
            </a>
        </div>
        @else
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Opération</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Note</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentTransfers as $transfer)
                    @php $isSender = $transfer->sender_id === $currentUser->id; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            @if($isSender)
                            <span class="inline-flex items-center gap-1.5 text-red-600 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                Virement vers {{ $transfer->recipient->name }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                                Reçu de {{ $transfer->sender->name }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $transfer->note ?? '–' }}</td>
                        <td class="px-6 py-4 text-right font-semibold {{ $isSender ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $isSender ? '-' : '+' }}{{ number_format($transfer->amount, 2, ',', ' ') }} €
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $transfer->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
