<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $announcements = Announcement::query()
            ->with('author')
            ->latest()
            ->get();

        return view('dashboard', compact('currentUser', 'announcements'));
    }
}
