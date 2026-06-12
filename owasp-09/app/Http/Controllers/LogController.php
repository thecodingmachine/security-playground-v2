<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class LogController extends Controller
{
    /**
     * ⚠️  VULNÉRABLE : XSS via visualisateur de logs
     * Le contenu du fichier de log est affiché sans encodage HTML ({!! !!}).
     * Toute entrée injectée par un utilisateur contenant du HTML ou du JavaScript
     * sera exécutée dans le navigateur de quiconque consulte cette page.
     */
    public function index(): View
    {
        $logPath = storage_path('logs/laravel.log');
        $lines = [];

        if (file_exists($logPath)) {
            /** @var list<string> $allLines */
            $allLines = file($logPath, FILE_IGNORE_NEW_LINES) ?: [];
            $lines = array_slice($allLines, -150);
        }

        return view('logs.index', compact('lines'));
    }
}
