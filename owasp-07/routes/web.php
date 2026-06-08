<?php

declare(strict_types=1);

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
});

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->middleware('auth')->name('admin.dashboard');
    Route::get('/api/employees', [AdminController::class, 'apiEmployees'])->name('admin.api.employees'); // ⚠️ aucun middleware d'authentification
});
