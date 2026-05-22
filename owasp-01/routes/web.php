<?php

declare(strict_types=1);

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Factures
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');       // ⚠️ IDOR
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy'); // ⚠️ méthode non protégée

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update'); // ⚠️ escalade de privilèges

    // Documents
    Route::get('/documents/download', [DocumentController::class, 'download'])->name('documents.download'); // ⚠️ path traversal

    // Administration — ⚠️ forced browsing (pas de vérification de rôle)
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Guide de challenges
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
});
