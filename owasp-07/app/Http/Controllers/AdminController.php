<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        abort_if($currentUser->role !== 'admin', 403);

        $usersCount = User::query()->count();
        $announcementsCount = Announcement::query()->count();
        $recentUsers = User::query()->latest()->take(5)->get();

        return view('admin.dashboard', compact('usersCount', 'announcementsCount', 'recentUsers'));
    }

    /**
     * ⚠️  VULNÉRABLE — Bypass par chemin alternatif
     * Cette route JSON n'est protégée par aucun middleware d'authentification.
     * Un commentaire HTML laissé par inadvertance dans la page de connexion révèle son existence.
     */
    public function apiEmployees(): JsonResponse
    {
        $employees = User::query()
            ->select(['id', 'name', 'email', 'role'])
            ->orderBy('name')
            ->get();

        return response()->json($employees);
    }
}
