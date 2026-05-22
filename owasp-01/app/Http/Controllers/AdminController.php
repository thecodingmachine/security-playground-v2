<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * ⚠️  VULNÉRABLE — Forced Browsing
     * La route est protégée par le middleware `auth` (authentification),
     * mais aucune vérification du rôle n'est effectuée côté serveur.
     * Le lien "Administration" est uniquement masqué côté client (Blade @if).
     * N'importe quel utilisateur authentifié peut accéder à /admin directement.
     */
    public function index(): View
    {
        $users = User::query()->withCount('invoices')->orderBy('name')->get();

        $stats = [
            'users' => User::query()->count(),
            'invoices' => Invoice::query()->count(),
            'revenue' => Invoice::query()->where('status', 'paid')->sum('amount'),
            'pending' => Invoice::query()->whereIn('status', ['sent', 'overdue'])->sum('amount'),
        ];

        $recentInvoices = Invoice::query()->with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('users', 'stats', 'recentInvoices'));
    }
}
