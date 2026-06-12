<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /**
     * ⚠️ VULNÉRABLE : Upload non restreint de fichiers
     *
     * Quatre erreurs combinées permettent l'exécution de code arbitraire :
     * 1. Aucune restriction d'extension ni de type MIME : n'importe quel fichier est accepté.
     * 2. Confiance dans getClientMimeType() : valeur fournie par le navigateur, non vérifiée.
     * 3. Nom original conservé : getClientOriginalName() peut contenir "shell.php".
     * 4. Stockage dans public/uploads/ : tout fichier est accessible par URL directe.
     */
    public function store(Request $request, ExpenseReport $expense): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        abort_if($expense->user_id !== $currentUser->id, 403);

        // ❌ Erreur 1 : aucune restriction de type ou de taille
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $file = $request->file('file');
        assert($file instanceof UploadedFile);

        // ❌ Erreur 2 : MIME type déclaré par le client, non vérifié par magic bytes
        $mimeType = $file->getClientMimeType();

        // ❌ Erreur 3 : nom original conservé tel quel (peut être "shell.php")
        $originalName = $file->getClientOriginalName();

        // ❌ Erreur 4 : stockage dans public/uploads/ : exécutable via URL directe
        $file->move(public_path('uploads'), $originalName);

        Attachment::query()->create([
            'expense_report_id' => $expense->id,
            'user_id' => $currentUser->id,
            'original_name' => $originalName,
            'stored_path' => 'uploads/'.$originalName, // ❌ Erreur 5 : chemin public retourné
            'mime_type' => $mimeType,
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Justificatif ajouté.');
    }
}
