<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VaultBank — Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-50 flex items-center justify-center">

<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">VaultBank</h1>
        <p class="text-sm text-gray-500 mt-1">Accès à votre espace bancaire</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                              {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-emerald-600">
                    <span class="text-sm text-gray-600">Se souvenir de moi</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Se connecter
            </button>
        </form>
    </div>
</div>

</body>
</html>
