<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — ExpenseCorp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center">
<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-600 rounded-xl mb-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">ExpenseCorp</h1>
        <p class="text-sm text-gray-500 mt-1">Gestion des notes de frais</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-8">
        <h2 class="text-base font-semibold text-gray-900 mb-6">Connexion</h2>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                              {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <button type="submit"
                    class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Se connecter
            </button>
        </form>
    </div>
</div>
</body>
</html>
