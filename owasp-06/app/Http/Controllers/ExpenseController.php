<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $expenses = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->orderByDesc('expense_date')
            ->get();

        return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        return view('expenses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'category' => ['required', 'in:transport,repas,hébergement,fournitures,autre'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expense_date' => ['required', 'date'],
        ]);

        /** @var array<string, mixed> $data */
        $data = $request->only(['title', 'amount', 'category', 'description', 'expense_date']);
        $data['user_id'] = $currentUser->id;

        $expense = ExpenseReport::query()->create($data);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Note de frais créée avec succès.');
    }

    public function show(ExpenseReport $expense): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        abort_if($expense->user_id !== $currentUser->id, 403);

        $expense->load('attachments');

        return view('expenses.show', compact('expense'));
    }
}
