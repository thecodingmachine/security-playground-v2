@extends('layouts.app')

@section('title', 'Challenges OWASP A01')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-md uppercase tracking-wider">OWASP A01:2021</span>
            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">Broken Access Control</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Contrôle d'accès défaillant</h1>
        <p class="text-sm text-gray-600 mt-2 max-w-3xl leading-relaxed">
            Cette application de gestion de factures contient <strong>5 vulnérabilités réelles</strong> de contrôle d'accès.
            Votre mission : les identifier, les exploiter, puis les corriger dans le code source.
        </p>
    </div>

    {{-- Accounts card --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-amber-900 mb-3">Comptes disponibles</h2>
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Utilisateur standard</p>
                <p class="font-mono text-xs text-gray-600">alice@example.com</p>
                <p class="font-mono text-xs text-gray-400">password</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Utilisateur standard</p>
                <p class="font-mono text-xs text-gray-600">bob@example.com</p>
                <p class="font-mono text-xs text-gray-400">password</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Administrateur</p>
                <p class="font-mono text-xs text-gray-600">admin@example.com</p>
                <p class="font-mono text-xs text-gray-400">password</p>
            </div>
        </div>
    </div>

    {{-- ───────────────── Challenge 1 ───────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Challenge 1</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">IDOR</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Consultation des factures d'un autre utilisateur</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong> et explorez la section <em>Mes factures</em>.
                Trouvez un moyen de consulter une facture appartenant à <strong>Bob Dupont</strong> sans utiliser son compte.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Observez attentivement la structure des URLs lorsque vous naviguez entre vos factures.</p>
                    <p class="text-sm text-amber-800">• Les identifiants de factures sont séquentiels. Alice a les factures 1–5, Bob a les factures 6–10.</p>
                </div>
            </details>
            <details class="border border-green-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-green-50 cursor-pointer text-sm font-medium text-green-800 select-none list-none">
                    <svg class="w-4 h-4 text-green-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler la solution & correction
                </summary>
                <div class="px-4 py-4 bg-green-50 border-t border-green-200 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Exploit</p>
                        <p class="text-sm text-green-700">Naviguer directement vers <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">/invoices/6</span> donne accès à la première facture de Bob.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE : InvoiceController::show()</span>
<span class="text-yellow-300">$invoice</span> = Invoice::query()->with([<span class="text-green-300">'user'</span>, <span class="text-green-300">'documents'</span>])<span class="text-red-400">->findOrFail($id)</span>;

<span class="text-green-400">// ✅ CORRIGÉ</span>
<span class="text-yellow-300">$invoice</span> = Invoice::query()->with([<span class="text-green-300">'user'</span>, <span class="text-green-300">'documents'</span>])
    -><span class="text-green-400">where</span>(<span class="text-green-300">'id'</span>, <span class="text-yellow-300">$id</span>)
    -><span class="text-green-400">where</span>(<span class="text-green-300">'user_id'</span>, <span class="text-blue-300">Auth::id()</span>)
    -><span class="text-green-400">firstOrFail</span>();</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Vérifier systématiquement l'ownership au niveau de chaque ressource (Object-Level Authorization), même pour un utilisateur authentifié.
                    </div>
                </div>
            </details>
        </div>
    </div>

    {{-- ───────────────── Challenge 2 ───────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Challenge 2</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Forced Browsing</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Accès non autorisé au panneau d'administration</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong> (rôle : user).
                Accédez au tableau de bord administrateur.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Le lien n'est pas visible dans le menu : cela suffit-il à protéger la page ?</p>
                    <p class="text-sm text-amber-800">• Quelle URL correspond habituellement à un panneau d'administration ?</p>
                </div>
            </details>
            <details class="border border-green-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-green-50 cursor-pointer text-sm font-medium text-green-800 select-none list-none">
                    <svg class="w-4 h-4 text-green-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler la solution & correction
                </summary>
                <div class="px-4 py-4 bg-green-50 border-t border-green-200 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Exploit</p>
                        <p class="text-sm text-green-700">Navigation directe vers <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">/admin</span>. La page s'affiche malgré le rôle <em>user</em>.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE : seul le middleware `auth` est actif</span>
<span class="text-blue-300">public function</span> <span class="text-yellow-300">index</span>(): View
{
    <span class="text-yellow-300">$users</span> = User::query()->withCount(<span class="text-green-300">'invoices'</span>)->get();
    <span class="text-blue-300">return</span> view(<span class="text-green-300">'admin.dashboard'</span>, compact(<span class="text-green-300">'users'</span>));
}

<span class="text-green-400">// ✅ CORRIGÉ : middleware dédié sur la route</span>
Route::<span class="text-yellow-300">get</span>(<span class="text-green-300">'/admin'</span>, [AdminController::class, <span class="text-green-300">'index'</span>])
    ->name(<span class="text-green-300">'admin.dashboard'</span>)
    ->middleware(<span class="text-green-300">'admin'</span>);

<span class="text-gray-500">// app/Http/Middleware/EnsureUserIsAdmin.php</span>
<span class="text-blue-300">public function</span> <span class="text-yellow-300">handle</span>(Request <span class="text-yellow-300">$request</span>, Closure <span class="text-yellow-300">$next</span>): Response
{
    <span class="text-blue-300">if</span> (<span class="text-yellow-300">$request</span>->user()?->role !== <span class="text-green-300">'admin'</span>) {
        <span class="text-yellow-300">abort</span>(<span class="text-orange-300">403</span>);
    }
    <span class="text-blue-300">return</span> <span class="text-yellow-300">$next</span>(<span class="text-yellow-300">$request</span>);
}</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Masquer un lien côté client via une directive Blade ne protège pas la route. La vérification du rôle doit être centralisée dans un middleware dédié, appliqué directement sur la route, pas dans le contrôleur.
                    </div>
                </div>
            </details>
        </div>
    </div>

    {{-- ───────────────── Challenge 3 ───────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-orange-600 uppercase tracking-wider">Challenge 3</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Escalade de privilèges</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Devenir administrateur via manipulation de requête</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong> et mettez à jour votre profil depuis <em>Mon profil</em>.
                Trouvez un moyen d'obtenir le rôle <strong>admin</strong>.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Interceptez la requête POST de mise à jour du profil avec les DevTools (onglet Network).</p>
                    <p class="text-sm text-amber-800">• Le formulaire ne contient que <em>nom</em> et <em>email</em> : le serveur n'accepterait-il pas d'autres paramètres ?</p>
                </div>
            </details>
            <details class="border border-green-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-green-50 cursor-pointer text-sm font-medium text-green-800 select-none list-none">
                    <svg class="w-4 h-4 text-green-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler la solution & correction
                </summary>
                <div class="px-4 py-4 bg-green-50 border-t border-green-200 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Exploit via console navigateur</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-blue-300">let</span> <span class="text-yellow-300">token</span> = document.querySelector(<span class="text-green-300">'meta[name="csrf-token"]'</span>).content;
fetch(<span class="text-green-300">'/profile'</span>, {
    method: <span class="text-green-300">'POST'</span>,
    body: <span class="text-blue-300">new</span> URLSearchParams({
        name: <span class="text-green-300">'Alice Martin'</span>, email: <span class="text-green-300">'alice@example.com'</span>,
        role: <span class="text-green-300">'admin'</span>, _token: <span class="text-yellow-300">token</span>
    })
}).then(() => location.reload());</code></pre>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE</span>
<span class="text-yellow-300">$data</span> = <span class="text-yellow-300">$request</span>->only([<span class="text-green-300">'name'</span>, <span class="text-green-300">'email'</span>, <span class="text-red-400">'role'</span>]);
<span class="text-yellow-300">$user</span>->update(<span class="text-yellow-300">$data</span>);

<span class="text-green-400">// ✅ CORRIGÉ : 'role' retiré de only()</span>
<span class="text-yellow-300">$data</span> = <span class="text-yellow-300">$request</span>->only([<span class="text-green-300">'name'</span>, <span class="text-green-300">'email'</span>]);
<span class="text-yellow-300">$user</span>->update(<span class="text-yellow-300">$data</span>);</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Le serveur doit limiter explicitement les champs acceptés aux seuls champs autorisés pour l'utilisateur courant. Ne jamais se fier à l'absence d'un champ dans le formulaire HTML.
                    </div>
                </div>
            </details>
        </div>
    </div>

    {{-- ───────────────── Challenge 4 ───────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Challenge 4</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Path Traversal</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Lecture de fichiers système via traversal de chemin</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong>. Certaines factures ont des documents PDF attachés, téléchargeables depuis leur page de détail.
                Trouvez un moyen de lire le fichier <strong>.env</strong> de l'application depuis cet endpoint de téléchargement.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Observez le paramètre utilisé dans l'URL de téléchargement.</p>
                    <p class="text-sm text-amber-800">• La séquence <span class="font-mono bg-amber-100 px-1 rounded">../</span> permet de remonter d'un niveau dans l'arborescence des dossiers.</p>
                </div>
            </details>
            <details class="border border-green-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-green-50 cursor-pointer text-sm font-medium text-green-800 select-none list-none">
                    <svg class="w-4 h-4 text-green-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler la solution & correction
                </summary>
                <div class="px-4 py-4 bg-green-50 border-t border-green-200 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Exploit</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs overflow-x-auto"><code>/documents/download?file=<span class="text-red-400">../../../.env</span>

<span class="text-gray-500">// storage/app/documents/../../../  →  racine du projet  →  .env</span></code></pre>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE</span>
<span class="text-yellow-300">$path</span> = storage_path(<span class="text-green-300">'app/documents/'</span> . <span class="text-red-400">$filename</span>);

<span class="text-green-400">// ✅ CORRIGÉ</span>
<span class="text-yellow-300">$allowedDir</span> = realpath(storage_path(<span class="text-green-300">'app/documents'</span>));
<span class="text-yellow-300">$resolved</span>   = realpath(<span class="text-yellow-300">$allowedDir</span> . DIRECTORY_SEPARATOR . <span class="text-yellow-300">$filename</span>);

<span class="text-blue-300">if</span> (<span class="text-yellow-300">$resolved</span> === <span class="text-orange-300">false</span> || !str_starts_with(<span class="text-yellow-300">$resolved</span>, <span class="text-yellow-300">$allowedDir</span>)) {
    abort(<span class="text-orange-300">403</span>);
}</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Résoudre le chemin avec <code>realpath()</code> et vérifier qu'il reste strictement contenu dans le répertoire autorisé.
                    </div>
                </div>
            </details>
        </div>
    </div>

    {{-- ───────────────── Challenge 5 ───────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Challenge 5</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">Méthode HTTP non protégée</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Suppression de la facture d'un autre utilisateur</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong>. Vous pouvez supprimer vos propres factures depuis leur page de détail.
                Trouvez un moyen de supprimer une facture appartenant à <strong>Bob Dupont</strong>.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Le bouton "Supprimer" envoie une requête avec une certaine méthode HTTP. Toutes les méthodes sont-elles protégées de la même façon ?</p>
                    <p class="text-sm text-amber-800">• Utilisez la console JavaScript du navigateur pour envoyer directement une requête de suppression.</p>
                    <p class="text-sm text-amber-800">• Si vous avez complété le challenge 1, vous connaissez déjà les IDs des factures de Bob.</p>
                </div>
            </details>
            <details class="border border-green-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-green-50 cursor-pointer text-sm font-medium text-green-800 select-none list-none">
                    <svg class="w-4 h-4 text-green-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler la solution & correction
                </summary>
                <div class="px-4 py-4 bg-green-50 border-t border-green-200 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Exploit via console navigateur</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-blue-300">const</span> <span class="text-yellow-300">token</span> = document.querySelector(<span class="text-green-300">'meta[name="csrf-token"]'</span>).content;
fetch(<span class="text-green-300">'/invoices/6'</span>, {
    method: <span class="text-green-300">'POST'</span>,
    headers: { <span class="text-green-300">'Content-Type'</span>: <span class="text-green-300">'application/x-www-form-urlencoded'</span> },
    body: <span class="text-green-300">'_method=DELETE&_token='</span> + <span class="text-yellow-300">token</span>
}).then(<span class="text-yellow-300">r</span> => console.log(<span class="text-yellow-300">r</span>.status)); <span class="text-gray-500">// 302 = succès</span></code></pre>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE : InvoiceController::destroy()</span>
<span class="text-yellow-300">$invoice</span> = Invoice::query()-><span class="text-red-400">findOrFail</span>(<span class="text-yellow-300">$id</span>);

<span class="text-green-400">// ✅ CORRIGÉ</span>
<span class="text-yellow-300">$invoice</span> = Invoice::query()
    ->where(<span class="text-green-300">'id'</span>, <span class="text-yellow-300">$id</span>)
    -><span class="text-green-400">where</span>(<span class="text-green-300">'user_id'</span>, <span class="text-blue-300">Auth::id()</span>)
    -><span class="text-green-400">firstOrFail</span>();</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> La vérification d'ownership doit être appliquée à <em>toutes</em> les méthodes HTTP. Chaque méthode est un point d'entrée distinct.
                    </div>
                </div>
            </details>
        </div>
    </div>

</div>
@endsection
