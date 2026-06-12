<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    /**
     * ⚠️  VULNÉRABLE : Path Traversal
     * Le paramètre `file` est utilisé directement pour construire le chemin
     * sans validation ni normalisation. Une séquence `../` permet de sortir
     * du répertoire autorisé et d'accéder à n'importe quel fichier du système.
     *
     * Exemple d'exploit : GET /documents/download?file=../../../.env
     */
    public function download(Request $request): BinaryFileResponse
    {
        $filename = $request->query('file', '');

        if (empty($filename)) {
            abort(400, 'Paramètre file manquant.');
        }

        $path = storage_path('app/documents/'.$filename);

        if (! file_exists($path)) {
            abort(404, 'Document introuvable.');
        }

        return response()->file($path);
    }
}
