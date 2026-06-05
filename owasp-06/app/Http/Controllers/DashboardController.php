<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $totalCount = ExpenseReport::query()->where('user_id', $currentUser->id)->count();

        $totalAmount = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->where('status', 'approuvée')
            ->sum('amount');

        $pendingCount = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->where('status', 'en_attente')
            ->count();

        $recentExpenses = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('dashboard', compact('totalCount', 'totalAmount', 'pendingCount', 'recentExpenses'));
    }
}
