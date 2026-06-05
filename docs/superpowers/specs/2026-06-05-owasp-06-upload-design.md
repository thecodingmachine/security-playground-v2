# Design — OWASP A06 : Upload non restreint de fichiers

**Date :** 2026-06-05
**Module :** `owasp-06/`
**Vulnérabilité ciblée :** OWASP A06:2021 — Composants vulnérables et obsolètes / Upload non restreint
**Stack :** PHP 8.5 · Laravel 13 · MySQL 8.4 · Tailwind CDN · Pest 4

---

## 1. Objectif

Implémenter **ExpenseCorp**, une application de gestion de notes de frais d'entreprise. Les employés soumettent leurs notes de frais et y attachent des justificatifs (reçus, factures). La fonctionnalité d'upload est volontairement vulnérable pour permettre l'exécution de code arbitraire via un webshell PHP.

L'application doit ressembler à une vraie application métier : la faille est enfouie dans un workflow normal et ne doit pas être évidente au premier regard.

---

## 2. Base de données

### Migrations

**Migration 1 — `add_role_to_users_table`**
Ajoute une colonne `role` (enum : `employee`, `admin`, default `employee`) à la table `users` existante.

**Migration 2 — `create_expense_reports_table`**

| Colonne | Type | Contraintes |
|---|---|---|
| `id` | bigint PK | auto-increment |
| `user_id` | unsignedBigInt | FK → users, cascade delete |
| `title` | string | |
| `amount` | decimal(10,2) | |
| `category` | enum | `transport`, `repas`, `hébergement`, `fournitures`, `autre` |
| `description` | text | nullable |
| `status` | enum | `en_attente`, `approuvée`, `rejetée` · default `en_attente` |
| `expense_date` | date | |
| `timestamps` | | |

**Migration 3 — `create_attachments_table`**

| Colonne | Type | Contraintes |
|---|---|---|
| `id` | bigint PK | auto-increment |
| `expense_report_id` | unsignedBigInt | FK → expense_reports, cascade delete |
| `user_id` | unsignedBigInt | FK → users, cascade delete |
| `original_name` | string | nom client non transformé (⚠️ vulnérabilité) |
| `stored_path` | string | chemin dans public/uploads/ (⚠️ vulnérabilité) |
| `mime_type` | string | valeur client non vérifiée (⚠️ vulnérabilité) |
| `timestamps` | | |

### Modèles & relations

- `User` → `hasMany(ExpenseReport)`, champ `role` (string)
- `ExpenseReport` → `belongsTo(User)`, `hasMany(Attachment)`
- `Attachment` → `belongsTo(ExpenseReport)`, `belongsTo(User)`

Toutes les relations annotées avec PHPDoc génériques (`HasMany<Model, $this>`, `BelongsTo<Model, $this>`).

### Factories & Seeder

**Utilisateurs seedés :**

| Nom | Email | Mot de passe | Rôle |
|---|---|---|---|
| Alice Moreau | alice@expensecorp.local | alice123 | employee |
| Bob Durand | bob@expensecorp.local | bob456 | employee |
| Admin RH | admin@expensecorp.local | rh-admin2024 | admin |

**Notes de frais seedées** (3–4 entrées réalistes) :
- 2 notes d'Alice (statuts variés : `approuvée` et `en_attente`)
- 1 note de Bob (`rejetée`)
- 1 note de Bob (`en_attente`)

---

## 3. Contrôleurs

### `AuthController`
- `showLogin(): View` — retourne `auth.login`
- `login(Request): RedirectResponse` — validation email/password, `Auth::login`, `$request->session()->regenerate()`, redirect dashboard
- `logout(Request): RedirectResponse` — `Auth::logout`, invalidate, regenerateToken

*Aucune vulnérabilité.*

### `DashboardController`
- `index(): View` — stats (total notes, montant total, nb en attente) + 3 dernières notes de l'utilisateur connecté

### `ExpenseController`
- `index(): View` — liste toutes les notes de l'utilisateur connecté (`ExpenseReport::query()->where('user_id', $currentUser->id)`)
- `create(): View` — formulaire de création
- `store(Request): RedirectResponse` — validation + création + redirect show
- `show(ExpenseReport): View` — détail + liste des attachments + formulaire d'upload

*Aucune vulnérabilité. Toutes les requêtes scoped sur `user_id`.*

### `AttachmentController` ⚠️ VULNÉRABLE

```php
// ⚠️ VULNÉRABLE — Upload non restreint de fichiers
public function store(Request $request, ExpenseReport $expense): RedirectResponse
{
    // ❌ Erreur 1 — validation inexistante ou trop permissive (aucun type, aucune taille)
    $request->validate(['file' => 'required|file']);

    $file = $request->file('file');

    // ❌ Erreur 2 — confiance dans le MIME type déclaré par le client
    $mimeType = $file->getClientMimeType();
    if (!in_array($mimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
        return back()->with('error', 'Type de fichier non autorisé.');
    }

    // ❌ Erreur 3 — conservation du nom original fourni par le client
    $originalName = $file->getClientOriginalName();

    // ❌ Erreur 4 — stockage dans public/uploads/ (exécutable via URL directe)
    $file->move(public_path('uploads'), $originalName);

    // Enregistrement en base avec le nom original et le chemin public
    Attachment::query()->create([
        'expense_report_id' => $expense->id,
        'user_id'           => $expense->user_id,
        'original_name'     => $originalName,
        'stored_path'       => 'uploads/' . $originalName,
        'mime_type'         => $mimeType,
    ]);

    return redirect()->route('expenses.show', $expense)
        ->with('success', 'Justificatif ajouté.');
}
```

Pas de route `download` dans la version vulnérable : les fichiers dans `public/uploads/` sont accessibles directement par URL (`asset('uploads/' . $attachment->original_name)`). La vue affiche ces liens directs dans la liste des justificatifs.

### `ChallengeController`
- `index(): View` — page `/challenges`, accessible à tous les utilisateurs connectés

---

## 4. Routes (`routes/web.php`)

```php
// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// App
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');

    // ⚠️ Endpoint vulnérable — upload sans restriction réelle
    Route::post('/expenses/{expense}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    // Note : pas de route download — les fichiers sont dans public/uploads/ et accessibles directement par URL

    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
});
```

---

## 5. Vues

### `layouts/app.blade.php`
Sidebar fixe (240px) + zone de contenu principale. Tailwind CDN.

**Sidebar :**
- Logo ExpenseCorp (icône reçu)
- Navigation : Tableau de bord, Mes notes de frais, Challenges OWASP
- Pied : avatar initiales + nom + badge rôle + bouton déconnexion

### Pages

| Fichier | Description |
|---|---|
| `auth/login.blade.php` | Page standalone (sans layout). Formulaire email + password. Pas d'indication des comptes disponibles. |
| `dashboard.blade.php` | 3 cards stats + tableau des 3 dernières notes avec badge de statut coloré |
| `expenses/index.blade.php` | Tableau complet des notes (titre, date, montant, catégorie, statut, actions). Bouton "Nouvelle note". |
| `expenses/create.blade.php` | Formulaire : titre, date, montant, catégorie (select), description (textarea). |
| `expenses/show.blade.php` | Détail de la note + section "Justificatifs" avec liste des fichiers + formulaire d'upload (`<input type="file">`). |
| `challenges/index.blade.php` | Une challenge card (voir section 6). |

---

## 6. Challenge card

### En-tête
- **Numéro :** Challenge 1
- **Badge type :** Upload non restreint
- **Titre :** Exécution de code via l'upload de justificatif
- **Difficulté :** ⭐⭐⭐

### Contexte (une phrase)
*"Connectez-vous en tant qu'Alice et créez une note de frais. Déposez un justificatif. Trouvez un moyen d'exécuter du code arbitraire sur le serveur."*

### Indice (accordéon `<details>`)
1. Quel composant du navigateur fournit le type MIME lors d'un upload, et peut-il être falsifié ?
2. Le nom du fichier est-il modifié avant d'être enregistré sur le disque ?
3. Où se retrouve le fichier une fois déposé ? Peut-on y accéder directement via une URL ?

### Solution & correction (accordéon `<details>`)

**Payload d'exploit :**
```
Créer un fichier "shell.php" contenant : <?php system($_GET['cmd']); ?>
Le renommer "shell.jpg" et falsifier le Content-Type en "image/jpeg".
Accéder à /uploads/shell.php?cmd=id pour exécuter des commandes.
```

**Code vulnérable ❌ vs corrigé ✅** (blocs `<pre><code>` avec coloration syntaxique inline) :
- Diff sur la validation (`'required|file'` → `['required','file','max:10240','mimes:jpeg,png,pdf']`)
- Diff sur le nom (`getClientOriginalName()` → `Str::random(32) . '.' . $file->extension()`)
- Diff sur le stockage (`public_path('uploads')` → `storeAs(..., disk: 'private')`)

**Principe OWASP :** Ne jamais faire confiance aux métadonnées fournies par le client (nom, MIME type). Stocker les fichiers hors du répertoire public avec un nom aléatoire.

---

## 7. Conventions techniques

Toutes les conventions du prompt de spec s'appliquent :
- `Model::query()->...` (jamais `Model::where(...)`)
- `Auth::user()` avec annotation `@var User $currentUser`
- PHPDoc génériques sur toutes les relations Eloquent
- `/** @var array<string, mixed> $data */` sur `$request->only()`
- Pint + PHPStan après chaque fichier généré

---

## 8. Ce qui n'est PAS dans le scope

- Section admin pour approuver/rejeter les notes (trop de surface pour un seul challenge)
- Export CSV
- Page de profil utilisateur
- Tests Pest (non demandés)
