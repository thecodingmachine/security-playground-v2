<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransferController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $transfers = Transfer::query()
            ->with(['sender', 'recipient'])
            ->where('sender_id', $currentUser->id)
            ->orWhere('recipient_id', $currentUser->id)
            ->latest()
            ->get();

        $recipients = User::query()
            ->where('id', '!=', $currentUser->id)
            ->orderBy('name')
            ->get();

        return view('transfers.index', compact('currentUser', 'transfers', 'recipients'));
    }

    /**
     * ⚠️  VULNÉRABLE — 3 failles combinées
     * 1. Données sensibles : les en-têtes HTTP (incluant le cookie de session) sont journalisés.
     * 2. Exception silencieuse : les erreurs de transfert ne sont pas journalisées.
     * 3. Injection via message : la note utilisateur est interpolée dans le message de log.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $validated = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id', Rule::notIn([$currentUser->id])],
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'note' => 'nullable|string|max:255',
        ]);

        /** @var float $amount */
        $amount = (float) $validated['amount'];
        /** @var int $recipientId */
        $recipientId = (int) $validated['recipient_id'];
        /** @var string $note */
        $note = (string) ($validated['note'] ?? '');

        // ⚠️ VULNÉRABLE — Challenge 2 : en-têtes HTTP journalisés (contient le cookie de session)
        Log::info('transfer_initiated', [
            'sender_id' => $currentUser->id,
            'recipient_id' => $recipientId,
            'amount' => $amount,
            'request_headers' => $request->headers->all(),
        ]);

        try {
            if ($currentUser->balance < $amount) {
                throw new \RuntimeException('Solde insuffisant');
            }

            DB::transaction(function () use ($currentUser, $recipientId, $amount, $note) {
                $currentUser->decrement('balance', $amount);
                User::query()->where('id', $recipientId)->increment('balance', $amount);
                Transfer::query()->create([
                    'sender_id' => $currentUser->id,
                    'recipient_id' => $recipientId,
                    'amount' => $amount,
                    'note' => $note,
                ]);
            });

            // ⚠️ VULNÉRABLE — Challenge 5 : note utilisateur interpolée dans le message de log
            Log::info("transfer_success note=\"{$note}\"");

            return redirect()->route('dashboard')->with('success', 'Virement effectué avec succès.');

        } catch (\Throwable $e) {
            // ⚠️ VULNÉRABLE — Challenge 4 : exception non journalisée — l'application est aveugle à cette erreur
            return back()->with('error', 'La transaction a échoué. Veuillez réessayer.');
        }
    }
}
