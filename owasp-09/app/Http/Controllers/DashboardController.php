<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $recentTransfers = Transfer::query()
            ->with(['sender', 'recipient'])
            ->where('sender_id', $currentUser->id)
            ->orWhere('recipient_id', $currentUser->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('currentUser', 'recentTransfers'));
    }
}
