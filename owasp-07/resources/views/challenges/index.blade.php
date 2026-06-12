@extends('layouts.app')

@section('title', 'Challenges OWASP A07')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-md uppercase tracking-wider">OWASP A07:2021</span>
            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">Identification and Authentication Failures</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Défaillances d'identification et d'authentification</h1>
        <p class="text-sm text-gray-600 mt-2 max-w-3xl leading-relaxed">
            Cet intranet d'entreprise contient <strong>5 vulnérabilités réelles</strong> d'authentification.
            Votre mission : les identifier, les exploiter, puis les corriger dans le code source.
        </p>
    </div>

    {{-- Accounts card --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-amber-900 mb-3">Comptes disponibles</h2>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Utilisateur standard</p>
                <p class="font-mono text-xs text-gray-600">bob@corp.local</p>
                <p class="font-mono text-xs text-gray-400">Tr0ub4dor&amp;3</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
                <p class="text-xs font-semibold text-gray-700 mb-1">Administrateur</p>
                <p class="font-mono text-xs text-gray-600">admin@corp.local</p>
                <p class="font-mono text-xs text-gray-400">K#9mP2$xL7vQ</p>
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Identifiants par défaut</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Connexion avec un compte de maintenance non sécurisé</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Un compte technique a été créé lors du déploiement initial de l'application. Son mot de passe par défaut n'a jamais été changé.
                Trouvez ce compte et connectez-vous avec.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Les comptes techniques sont souvent nommés <em>maintenance</em>, <em>support</em> ou <em>admin</em>.</p>
                    <p class="text-sm text-amber-800">• Le domaine des e-mails de l'entreprise est <span class="font-mono bg-amber-100 px-1 rounded">corp.local</span>.</p>
                    <p class="text-sm text-amber-800">• Les mots de passe par défaut les plus courants sont <span class="font-mono bg-amber-100 px-1 rounded">admin</span>, <span class="font-mono bg-amber-100 px-1 rounded">password</span>, <span class="font-mono bg-amber-100 px-1 rounded">123456</span>.</p>
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
                        <p class="text-sm text-green-700">Se connecter avec <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">maintenance@corp.local</span> / <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">admin</span> donne accès à un compte administrateur.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Correction</p>
                        <p class="text-sm text-green-700">Il n'y a pas de correction dans le code : c'est un problème de configuration et de processus de déploiement.</p>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Tout mot de passe par défaut doit être modifié avant la mise en production. Les comptes techniques inutilisés doivent être désactivés ou supprimés.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Énumération de comptes</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Découverte des comptes existants via les messages d'erreur</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                La page de connexion retourne des messages d'erreur différents selon que l'adresse e-mail existe ou non.
                Démontrez que cette différence permet de confirmer l'existence d'un compte.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Essayez de vous connecter avec une adresse inexistante (<span class="font-mono bg-amber-100 px-1 rounded">inconnu@corp.local</span>) puis un mauvais mot de passe.</p>
                    <p class="text-sm text-amber-800">• Essayez ensuite avec <span class="font-mono bg-amber-100 px-1 rounded">bob@corp.local</span> et un mauvais mot de passe. Comparez les messages.</p>
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
                        <p class="text-sm text-green-700 mb-2">Les deux messages révèlent l'existence ou non du compte :</p>
                        <ul class="text-sm text-green-700 space-y-1">
                            <li>• Adresse inconnue : <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">Aucun compte associé à cette adresse.</span></li>
                            <li>• Adresse connue, mauvais mot de passe : <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">Mot de passe incorrect.</span></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE : AuthController::login()</span>
<span class="text-blue-300">if</span> (!<span class="text-yellow-300">$user</span>) {
    <span class="text-blue-300">return</span> back()->withErrors([<span class="text-green-300">'email'</span> => <span class="text-red-400">'Aucun compte associé à cette adresse.'</span>]);
}
<span class="text-blue-300">if</span> (!Hash::check(<span class="text-yellow-300">$request</span>->password, <span class="text-yellow-300">$user</span>->password)) {
    <span class="text-blue-300">return</span> back()->withErrors([<span class="text-green-300">'email'</span> => <span class="text-red-400">'Mot de passe incorrect.'</span>]);
}

<span class="text-green-400">// ✅ CORRIGÉ : message générique unique</span>
<span class="text-blue-300">if</span> (!<span class="text-yellow-300">$user</span> || !Hash::check(<span class="text-yellow-300">$request</span>->password, <span class="text-yellow-300">$user</span>->password)) {
    <span class="text-blue-300">return</span> back()->withErrors([<span class="text-green-300">'email'</span> => <span class="text-green-300">'Identifiants incorrects.'</span>]);
}</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Toujours retourner un message d'erreur identique, quel que soit le motif de l'échec. Ne jamais indiquer si l'email existe ou non.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Brute-force</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Récupération du mot de passe d'Alice par force brute</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Le compte <strong>alice@corp.local</strong> utilise un mot de passe faible. L'application ne limite pas le nombre de tentatives de connexion.
                Retrouvez son mot de passe par force brute.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Envoyez des requêtes POST en boucle sur <span class="font-mono bg-amber-100 px-1 rounded">/login</span> en changeant uniquement le champ <span class="font-mono bg-amber-100 px-1 rounded">password</span>.</p>
                    <p class="text-sm text-amber-800">• Utilisez le challenge 2 pour distinguer "mauvais mot de passe" (compte existant) d'un "compte inexistant".</p>
                    <p class="text-sm text-amber-800">• Testez des mots de passe courants : <span class="font-mono bg-amber-100 px-1 rounded">alice</span>, <span class="font-mono bg-amber-100 px-1 rounded">alice123</span>, <span class="font-mono bg-amber-100 px-1 rounded">password</span>, <span class="font-mono bg-amber-100 px-1 rounded">123456</span>...</p>
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
                        <p class="text-sm text-green-700 mb-2">Le mot de passe d'Alice est <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">alice123</span>. Script bash :</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-gray-500"># Récupérer le token CSRF depuis la page de connexion</span>
TOKEN=$(curl -sc /tmp/cookies https://owasp.localhost/login | \
  grep -oP 'name="token" value="\K[^"]+')

<span class="text-blue-300">for</span> password <span class="text-blue-300">in</span> alice password alice123 123456 qwerty; <span class="text-blue-300">do</span>
  STATUS=$(curl -sb /tmp/cookies -c /tmp/cookies -s -o /dev/null -w "%{http_code}" \
    -X POST https://owasp.localhost/login \
    -d "email=alice%40corp.local&password=$password&_token=$TOKEN")
  echo "$password -> $STATUS"
<span class="text-blue-300">done</span>
<span class="text-gray-500"># HTTP 302 = succès (redirection post-login)</span></code></pre>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Correction</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-green-400">// ✅ CORRIGÉ : ajouter le middleware throttle sur la route de login</span>
<span class="text-gray-500">// routes/web.php</span>
Route::post(<span class="text-green-300">'/login'</span>, [AuthController::class, <span class="text-green-300">'login'</span>])
    -><span class="text-green-400">middleware</span>(<span class="text-green-300">'throttle:5,1'</span>); <span class="text-gray-500">// 5 tentatives par minute</span></code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Limiter le nombre de tentatives par IP et par compte (middleware <code>throttle</code>). Envisager un délai progressif ou un CAPTCHA après plusieurs échecs.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Fixation de session</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Session non régénérée après authentification</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Ouvrez les outils de développement (<em>Application &gt; Cookies</em>), notez l'identifiant de session avant la connexion, puis connectez-vous.
                Vérifiez si la valeur du cookie a changé.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Ouvrez DevTools (F12) > onglet <em>Application</em> > <em>Cookies</em>.</p>
                    <p class="text-sm text-amber-800">• Notez la valeur du cookie <span class="font-mono bg-amber-100 px-1 rounded">laravel_session</span> présent sur la page de login.</p>
                    <p class="text-sm text-amber-800">• Après connexion, comparez la valeur : si elle est identique, la session n'a pas été régénérée.</p>
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
                        <p class="text-sm text-green-700">L'identifiant de session est identique avant et après la connexion. Un attaquant qui a préalablement fixé un identifiant de session connu (via une autre faille) récupère automatiquement la session authentifiée de la victime.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE : AuthController::login()</span>
Auth::login(<span class="text-yellow-300">$user</span>, <span class="text-yellow-300">$request</span>->boolean(<span class="text-green-300">'remember'</span>));
<span class="text-red-400">// session non régénérée</span>
<span class="text-blue-300">return</span> redirect()->intended(route(<span class="text-green-300">'dashboard'</span>));

<span class="text-green-400">// ✅ CORRIGÉ</span>
Auth::login(<span class="text-yellow-300">$user</span>, <span class="text-yellow-300">$request</span>->boolean(<span class="text-green-300">'remember'</span>));
<span class="text-yellow-300">$request</span>->session()-><span class="text-green-400">regenerate</span>();
<span class="text-blue-300">return</span> redirect()->intended(route(<span class="text-green-300">'dashboard'</span>));</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Toujours régénérer l'identifiant de session après un changement de niveau de privilège (connexion, déconnexion, changement de rôle) pour prévenir les attaques par fixation de session.
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">Chemin alternatif</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Endpoint d'administration accessible sans authentification</h3>
                </div>
                <span class="text-amber-400 text-sm">⭐⭐</span>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Consultez le code source HTML de la page de connexion. Un commentaire de développeur révèle un endpoint oublié en production.
                Accédez-y sans être connecté.
            </p>
            <details class="border border-amber-200 rounded-lg overflow-hidden group">
                <summary class="flex items-center gap-2 px-4 py-3 bg-amber-50 cursor-pointer text-sm font-medium text-amber-800 select-none list-none">
                    <svg class="w-4 h-4 text-amber-500 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Révéler l'indice
                </summary>
                <div class="px-4 py-3 bg-amber-50 border-t border-amber-200 space-y-1.5">
                    <p class="text-sm text-amber-800">• Affichez le source de la page de connexion (Ctrl+U) et cherchez un commentaire <span class="font-mono bg-amber-100 px-1 rounded">TODO</span>.</p>
                    <p class="text-sm text-amber-800">• Testez l'URL trouvée dans un nouvel onglet sans être connecté.</p>
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
                        <p class="text-sm text-green-700 mb-2">Le source de la page de connexion contient :</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs overflow-x-auto"><code><span class="text-gray-500">&lt;!-- TODO: désactiver avant la livraison : /admin/api/employees --&gt;</span></code></pre>
                        <p class="text-sm text-green-700 mt-2">Accéder à <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">/admin/api/employees</span> sans être connecté retourne la liste complète des collaborateurs au format JSON.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable → corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE : routes/web.php</span>
Route::prefix(<span class="text-green-300">'admin'</span>)->group(function () {
    Route::get(<span class="text-green-300">'/'</span>, [AdminController::class, <span class="text-green-300">'index'</span>])->middleware(<span class="text-green-300">'auth'</span>);
    Route::get(<span class="text-green-300">'/api/employees'</span>, [AdminController::class, <span class="text-green-300">'apiEmployees'</span>]); <span class="text-red-400">// pas de middleware</span>
});

<span class="text-green-400">// ✅ CORRIGÉ : middleware auth sur tout le groupe</span>
Route::prefix(<span class="text-green-300">'admin'</span>)->middleware(<span class="text-green-300">'auth'</span>)->group(function () {
    Route::get(<span class="text-green-300">'/'</span>, [AdminController::class, <span class="text-green-300">'index'</span>]);
    Route::get(<span class="text-green-300">'/api/employees'</span>, [AdminController::class, <span class="text-green-300">'apiEmployees'</span>]);
});</code></pre>
                    </div>
                    <div class="text-xs text-green-700 bg-green-100 rounded-lg px-3 py-2">
                        <strong>Principe :</strong> Chaque endpoint est un point d'accès indépendant. Le middleware d'authentification doit être appliqué explicitement à toutes les routes, y compris les routes API internes.
                    </div>
                </div>
            </details>
        </div>
    </div>

</div>
@endsection
