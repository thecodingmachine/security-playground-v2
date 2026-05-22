@extends('layouts.app')

@section('title', 'Challenges OWASP A09')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-md uppercase tracking-wider">OWASP A09:2021</span>
            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">Security Logging & Alerting Failures</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Défaillances de journalisation et d'alerting</h1>
        <p class="text-sm text-gray-600 mt-2 max-w-3xl leading-relaxed">
            Cette application bancaire contient <strong>5 vulnérabilités réelles</strong> liées à la journalisation.
            Votre mission : les identifier, les exploiter, puis les corriger dans le code source.
        </p>
    </div>

    {{-- Accounts card --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-amber-900 mb-3">Comptes disponibles</h2>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Utilisateur standard</p>
                <p class="font-mono text-xs text-gray-600">alice@bank.local</p>
                <p class="font-mono text-xs text-gray-400">alice123</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Utilisateur standard</p>
                <p class="font-mono text-xs text-gray-600">bob@bank.local</p>
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Journalisation insuffisante</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Les tentatives de connexion échouées ne laissent aucune trace</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Effectuez plusieurs tentatives de connexion échouées sur le compte d'<strong>Alice</strong>, puis connectez-vous avec ses identifiants corrects.
                Consultez ensuite le journal applicatif depuis <em>Journal applicatif</em> dans le menu.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Déconnectez-vous, puis tentez de vous connecter avec <span class="font-mono bg-amber-100 px-1 rounded">alice@bank.local</span> et trois mauvais mots de passe.</p>
                    <p class="text-sm text-amber-800">• Connectez-vous ensuite avec les bons identifiants, allez dans <em>Journal applicatif</em> et cherchez les traces des tentatives échouées.</p>
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
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Résultat observé</p>
                        <p class="text-sm text-green-700">Seule la connexion réussie génère une entrée de type <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">authn_login_success</span>. Les 3 tentatives échouées sont invisibles — un attaquant peut faire du brute-force sans laisser aucune trace.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE — AuthController::login()</span>
<span class="text-blue-300">if</span> (Auth::attempt(<span class="text-yellow-300">$credentials</span>)) {
    Log::info(<span class="text-green-300">'authn_login_success'</span>, [...]);
    <span class="text-blue-300">return</span> redirect()->intended(...);
}
<span class="text-red-400">// Échec : rien n'est journalisé</span>
<span class="text-blue-300">return</span> back()->withErrors([...]);

<span class="text-green-400">// ✅ CORRIGÉ</span>
<span class="text-blue-300">if</span> (Auth::attempt(<span class="text-yellow-300">$credentials</span>)) {
    Log::info(<span class="text-green-300">'authn_login_success'</span>, [<span class="text-green-300">'email'</span> => <span class="text-yellow-300">$email</span>, <span class="text-green-300">'ip'</span> => <span class="text-yellow-300">$ip</span>]);
    <span class="text-blue-300">return</span> redirect()->intended(...);
}
<span class="text-green-400">Log::warning</span>(<span class="text-green-300">'authn_login_failure'</span>, [<span class="text-green-300">'email'</span> => <span class="text-yellow-300">$email</span>, <span class="text-green-300">'ip'</span> => <span class="text-yellow-300">$ip</span>]);
<span class="text-blue-300">return</span> back()->withErrors([...]);</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Toujours journaliser les échecs d'authentification avec l'email, l'IP et l'horodatage. Ces événements sont le principal signal de détection des attaques par force brute.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Données sensibles dans les logs</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Le cookie de session est journalisé en clair lors d'un virement</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong>, effectuez un virement, puis consultez le journal applicatif.
                Repérez une donnée sensible liée à votre session dans les logs.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Effectuez un virement d'un faible montant vers Bob, puis ouvrez le journal applicatif.</p>
                    <p class="text-sm text-amber-800">• Dans l'entrée <span class="font-mono bg-amber-100 px-1 rounded">transfer_initiated</span>, cherchez la clé <span class="font-mono bg-amber-100 px-1 rounded">cookie</span> dans les en-têtes journalisés.</p>
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
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Données exposées</p>
                        <p class="text-sm text-green-700 mb-2">Le log <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">transfer_initiated</span> contient <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">request_headers</span> avec le cookie de session en clair :</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs overflow-x-auto"><code>"cookie":["laravel_session=<span class="text-red-400">eyJpdiI6Ik...</span>"]</code></pre>
                        <p class="text-sm text-green-700 mt-2">Quiconque accède à ce fichier de log peut usurper n'importe quelle session active.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE — TransferController::store()</span>
Log::info(<span class="text-green-300">'transfer_initiated'</span>, [
    <span class="text-green-300">'sender_id'</span> => <span class="text-yellow-300">$currentUser</span>->id,
    <span class="text-green-300">'amount'</span> => <span class="text-yellow-300">$amount</span>,
    <span class="text-red-400">'request_headers'</span> => <span class="text-yellow-300">$request</span>->headers->all(), <span class="text-red-400">// ← cookie de session</span>
]);

<span class="text-green-400">// ✅ CORRIGÉ — seulement les métadonnées métier</span>
Log::info(<span class="text-green-300">'transfer_initiated'</span>, [
    <span class="text-green-300">'sender_id'</span> => <span class="text-yellow-300">$currentUser</span>->id,
    <span class="text-green-300">'recipient_id'</span> => <span class="text-yellow-300">$recipientId</span>,
    <span class="text-green-300">'amount'</span> => <span class="text-yellow-300">$amount</span>,
    <span class="text-green-300">'ip'</span> => <span class="text-yellow-300">$request</span>->ip(),
]);</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Ne journaliser que les métadonnées strictement nécessaires à l'audit métier. Les cookies, tokens d'autorisation, mots de passe et clés ne doivent jamais apparaître dans les logs.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Injection de logs</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Fabrication de fausses entrées de log via le nom de profil</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong>. Le contrôleur de profil interpole le nom directement dans le message de log.
                Envoyez un nom contenant un retour à la ligne pour injecter une fausse entrée dans le journal.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Le champ <em>Nom complet</em> dans <em>Mon profil</em> est ensuite interpolé dans un message de log (<span class="font-mono bg-amber-100 px-1 rounded">Log::info("profile_updated: {$name}")</span>).</p>
                    <p class="text-sm text-amber-800">• Un retour à la ligne dans le nom crée une nouvelle ligne dans le fichier de log, que vous pouvez contrôler entièrement.</p>
                    <p class="text-sm text-amber-800">• Utilisez la console navigateur pour envoyer un nom contenant <span class="font-mono bg-amber-100 px-1 rounded">\n</span>.</p>
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
<span class="text-blue-300">const</span> <span class="text-yellow-300">fakeEntry</span> = <span class="text-green-300">'[2025-01-15 09:41:00] production.INFO: authn_login_success {"email":"admin@bank.local","role":"admin"} [] []'</span>;
fetch(<span class="text-green-300">'/profile'</span>, {
    method: <span class="text-green-300">'POST'</span>,
    body: <span class="text-blue-300">new</span> URLSearchParams({
        name: <span class="text-green-300">'Alice Dupont\n'</span> + <span class="text-yellow-300">fakeEntry</span>,
        email: <span class="text-green-300">'alice@bank.local'</span>,
        _token: <span class="text-yellow-300">token</span>
    })
}).then(() => location.assign(<span class="text-green-300">'/logs'</span>));</code></pre>
                        <p class="text-sm text-green-700 mt-2">Le journal affiche alors une connexion admin réussie qui n'a jamais eu lieu.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE — ProfileController::update()</span>
Log::info(<span class="text-red-400">"profile_updated: {$name}"</span>); <span class="text-red-400">// interpolation directe</span>

<span class="text-green-400">// ✅ CORRIGÉ — données dans le contexte JSON</span>
Log::info(<span class="text-green-300">'profile_updated'</span>, [
    <span class="text-green-300">'user_id'</span> => <span class="text-yellow-300">$user</span>->id,
    <span class="text-green-300">'name'</span> => preg_replace(<span class="text-green-300">'/[\x00-\x1F\x7F]/u'</span>, <span class="text-green-300">''</span>, mb_substr(<span class="text-yellow-300">$name</span>, <span class="text-orange-300">0</span>, <span class="text-orange-300">256</span>)),
]);</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Passer les données utilisateur dans le tableau de contexte (2e argument de Log). Monolog sérialise ce tableau en JSON, ce qui échappe automatiquement les retours à la ligne. En défense en profondeur, supprimer les caractères de contrôle avant la journalisation.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Exception silencieuse</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Une erreur de transaction est avalée sans aucune trace dans les logs</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Connectez-vous en tant qu'<strong>Alice</strong> et tentez un virement d'un montant supérieur à votre solde.
                Observez ensuite le journal applicatif : que se passe-t-il côté logs ?
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Tentez un virement de <span class="font-mono bg-amber-100 px-1 rounded">99 999 €</span> (montant très supérieur au solde d'Alice).</p>
                    <p class="text-sm text-amber-800">• L'interface affiche un message d'erreur. Vérifiez si une entrée <span class="font-mono bg-amber-100 px-1 rounded">transfer_failure</span> apparaît dans les logs.</p>
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
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Résultat observé</p>
                        <p class="text-sm text-green-700">Le log montre l'entrée <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">transfer_initiated</span> mais aucune entrée <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">transfer_failure</span>. L'application est aveugle à sa propre erreur — un opérateur de supervision ne saurait jamais que des transactions échouent.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE — TransferController::store()</span>
} <span class="text-blue-300">catch</span> (\Throwable <span class="text-yellow-300">$e</span>) {
    <span class="text-red-400">// Exception silencieuse — aucune trace</span>
    <span class="text-blue-300">return</span> back()->with(<span class="text-green-300">'error'</span>, <span class="text-green-300">'La transaction a échoué.'</span>);
}

<span class="text-green-400">// ✅ CORRIGÉ</span>
} <span class="text-blue-300">catch</span> (\Throwable <span class="text-yellow-300">$e</span>) {
    <span class="text-green-400">Log::error</span>(<span class="text-green-300">'transfer_failure'</span>, [
        <span class="text-green-300">'sender_id'</span> => <span class="text-yellow-300">$currentUser</span>->id,
        <span class="text-green-300">'amount'</span> => <span class="text-yellow-300">$amount</span>,
        <span class="text-green-300">'error'</span> => <span class="text-yellow-300">$e</span>->getMessage(),
    ]);
    <span class="text-blue-300">return</span> back()->with(<span class="text-green-300">'error'</span>, <span class="text-green-300">'La transaction a échoué.'</span>);
}</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Tout bloc catch doit journaliser l'exception avec au minimum le message et les données contextuelles. La trace complète (<code>getTraceAsString()</code>) ne doit jamais être renvoyée au client, mais doit apparaître dans les logs serveur.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">XSS via visualisateur de logs</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Du code JavaScript s'exécute depuis le journal applicatif</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Le visualisateur de logs affiche le contenu brut sans encodage HTML.
                Effectuez un virement avec un motif contenant du HTML, puis ouvrez le journal applicatif.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Le motif d'un virement est journalisé via une interpolation de chaîne dans le message de log.</p>
                    <p class="text-sm text-amber-800">• Le visualisateur affiche les logs avec @verbatim<span class="font-mono bg-amber-100 px-1 rounded">{!! $line !!}</span>@endverbatim — sans encodage HTML.</p>
                    <p class="text-sm text-amber-800">• Essayez un motif de type : <span class="font-mono bg-amber-100 px-1 rounded text-xs">&lt;img src=x onerror="alert(1)"&gt;</span></p>
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
                        <p class="text-sm text-green-700 mb-2">Effectuer un virement avec ce motif :</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs overflow-x-auto"><code>&lt;img src=x onerror="alert('XSS: ' + document.cookie)"&gt;</code></pre>
                        <p class="text-sm text-green-700 mt-2">En visitant <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">/logs</span>, le navigateur interprète le tag HTML et exécute le gestionnaire <span class="font-mono text-xs">onerror</span>.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
@verbatim
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">{{-- ❌ VULNÉRABLE — logs/index.blade.php --}}</span>
&lt;div&gt;{!! <span class="text-yellow-300">$line</span> !!}&lt;/div&gt; <span class="text-red-400">{{-- HTML brut --}}</span>

<span class="text-green-400">{{-- ✅ CORRIGÉ — encodage automatique par Blade --}}</span>
&lt;div&gt;{{ <span class="text-yellow-300">$line</span> }}&lt;/div&gt;</code></pre>
@endverbatim
                    </div>
@verbatim
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Ne jamais afficher du contenu issu de sources non maîtrisées avec <code>{!! !!}</code>. La syntaxe <code>{{ }}</code> de Blade encode automatiquement les caractères HTML. Les logs sont une surface d'attaque : ils peuvent contenir des payloads injectés par des utilisateurs malveillants.
                    </div>
@endverbatim
                </div>
            </details>
        </div>
    </div>

</div>
@endsection
