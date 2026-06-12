@extends('layouts.app')

@section('title', 'Virements')

@section('content')
<div class="space-y-8">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Virements</h1>
        <p class="text-sm text-gray-500 mt-1">Solde disponible :
            <span class="font-semibold text-emerald-700">{{ number_format($currentUser->balance, 2, ',', ' ') }} €</span>
        </p>
    </div>

    {{-- Transfer form --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-5">Nouveau virement</h2>
        <form method="POST" action="{{ route('transfers.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="recipient_id" class="block text-sm font-medium text-gray-700 mb-1.5">Bénéficiaire</label>
                    <select id="recipient_id" name="recipient_id" required
                            class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                                   {{ $errors->has('recipient_id') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <option value="">Sélectionner...</option>
                        @foreach($recipients as $recipient)
                        <option value="{{ $recipient->id }}" {{ old('recipient_id') == $recipient->id ? 'selected' : '' }}>
                            {{ $recipient->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('recipient_id')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Montant (€)</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                           min="0.01" max="999999.99" step="0.01" required
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                                  {{ $errors->has('amount') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    @error('amount')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <label for="note" class="block text-sm font-medium text-gray-700 mb-1.5">Motif <span class="text-gray-400 font-normal">(optionnel)</span></label>
                <input type="text" id="note" name="note" value="{{ old('note') }}" maxlength="255"
                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
            <div>
                <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Confirmer le virement
                </button>
            </div>
        </form>
    </div>

    {{-- Transfer history --}}
    <div>
        <h2 class="text-base font-semibold text-gray-900 mb-4">Historique complet</h2>
        @if($transfers->isEmpty())
        <p class="text-sm text-gray-500">Aucune opération.</p>
        @else
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Opération</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Motif</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transfers as $transfer)
                    @php $isSender = $transfer->sender_id === $currentUser->id; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            @if($isSender)
                            <span class="inline-flex items-center gap-1.5 text-red-600 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                {{ $transfer->recipient->name }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                                {{ $transfer->sender->name }}
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
