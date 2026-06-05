@extends('layouts.app')

@section('title', $expense->title)

@section('content')
<div class="space-y-6 max-w-3xl">

    <div>
        <a href="{{ route('expenses.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Mes notes de frais
        </a>
        <div class="flex items-start justify-between">
            <h1 class="text-2xl font-bold text-gray-900">{{ $expense->title }}</h1>
            @php
                $statusClasses = match($expense->status) {
                    'approuvée' => 'bg-green-100 text-green-700',
                    'rejetée'   => 'bg-red-100 text-red-700',
                    default     => 'bg-amber-100 text-amber-700',
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses }}">
                {{ ucfirst($expense->status) }}
            </span>
        </div>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        <div class="grid grid-cols-3 divide-x divide-gray-100">
            <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Montant</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format((float) $expense->amount, 2, ',', ' ') }} €</p>
            </div>
            <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Date</p>
                <p class="text-sm font-medium text-gray-900">{{ $expense->expense_date->format('d/m/Y') }}</p>
            </div>
            <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catégorie</p>
                <p class="text-sm font-medium text-gray-900 capitalize">{{ $expense->category }}</p>
            </div>
        </div>
        @if($expense->description)
        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</p>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $expense->description }}</p>
        </div>
        @endif
    </div>

    {{-- Attachments --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Justificatifs</h2>
        </div>

        @if($expense->attachments->isNotEmpty())
        <div class="divide-y divide-gray-100">
            @foreach($expense->attachments as $attachment)
            <div class="px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $attachment->original_name }}</p>
                        <p class="text-xs text-gray-400">{{ $attachment->mime_type }}</p>
                    </div>
                </div>
                <a href="{{ asset($attachment->stored_path) }}"
                   target="_blank"
                   class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                    Ouvrir
                </a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Upload form --}}
        @if($expense->status === 'en_attente')
        <div class="px-6 py-5 bg-gray-50 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ajouter un justificatif</p>
            <form method="POST" action="{{ route('attachments.store', $expense) }}" enctype="multipart/form-data"
                  class="flex items-center gap-3">
                @csrf
                <input type="file" name="file" required
                       class="block text-sm text-gray-600
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100 cursor-pointer">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shrink-0">
                    Déposer
                </button>
            </form>
            @error('file')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        @endif
    </div>

</div>
@endsection
