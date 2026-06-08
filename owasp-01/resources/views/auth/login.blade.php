<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FacturaPro — Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-50 flex items-center justify-center px-4">

    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-xl mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">FacturaPro</h1>
            <p class="text-sm text-gray-500 mt-1">Connectez-vous à votre espace</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Adresse email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm text-gray-900 placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                               {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                        placeholder="vous@exemple.com">
                    @error('email')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Mot de passe
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center gap-2">
                    <input id="remember" type="checkbox" name="remember" value="1"
                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="text-sm text-gray-600">Se souvenir de moi</label>
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                               rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Se connecter
                </button>
            </form>
        </div>

        {{-- Comptes de test --}}
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <p class="text-xs font-semibold text-amber-800 mb-2">Comptes de démonstration</p>
            <div class="space-y-1.5 text-xs text-amber-700 font-mono">
                <div class="flex items-center justify-between gap-2">
                    <span>alice@example.com · <span class="font-semibold">password</span></span>
                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-sans font-medium">user</span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span>bob@example.com · <span class="font-semibold">password</span></span>
                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-sans font-medium">user</span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span>admin@example.com · <span class="font-semibold">password</span></span>
                    <span class="px-1.5 py-0.5 bg-red-100 text-red-600 rounded text-[10px] font-sans font-medium">admin</span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
