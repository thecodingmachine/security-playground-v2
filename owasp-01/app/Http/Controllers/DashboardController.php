<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $recentInvoices = Invoice::query()
            ->where('user_id', $currentUser->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total' => Invoice::query()->where('user_id', $currentUser->id)->count(),
            'paid' => Invoice::query()->where('user_id', $currentUser->id)->where('status', 'paid')->count(),
            'overdue' => Invoice::query()->where('user_id', $currentUser->id)->where('status', 'overdue')->count(),
            'revenue' => Invoice::query()->where('user_id', $currentUser->id)->where('status', 'paid')->sum('amount'),
        ];

        return view('dashboard', compact('recentInvoices', 'stats'));
    }
}
