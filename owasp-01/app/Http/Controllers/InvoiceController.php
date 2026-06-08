<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $invoices = Invoice::query()
            ->where('user_id', $currentUser->id)
            ->latest()
            ->get();

        return view('invoices.index', compact('invoices'));
    }

    /**
     * ⚠️  VULNÉRABLE — IDOR (Insecure Direct Object Reference)
     * L'identifiant provient directement de l'URL sans vérification d'ownership.
     * Un utilisateur authentifié peut accéder aux factures d'autres utilisateurs
     * en changeant simplement l'ID dans l'URL.
     */
    public function show(int $id): View
    {
        $invoice = Invoice::query()->with(['user', 'documents'])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * ⚠️  VULNÉRABLE — Méthode HTTP non protégée
     * La route GET /invoices/{id} vérifie l'ownership (via show()),
     * mais cette route DELETE ne le fait pas.
     * Un attaquant peut supprimer n'importe quelle facture.
     */
    public function destroy(int $id): RedirectResponse
    {
        $invoice = Invoice::query()->findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Facture supprimée avec succès.');
    }
}
