<?php

namespace Database\Seeders;

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
            'title' => 'Déjeuner client Accenture - Paris 8e',
            'amount' => 87.50,
            'category' => 'repas',
            'description' => 'Déjeuner de travail avec le directeur technique d\'Accenture pour la présentation du projet Q2.',
            'expense_date' => '2026-05-14',
        ]);

        ExpenseReport::factory()->create([
            'user_id' => $alice->id,
            'title' => 'Train Paris-Lyon - séminaire annuel',
            'amount' => 124.00,
            'category' => 'transport',
            'description' => 'Billet TGV aller-retour pour le séminaire commercial du 28 mai.',
            'expense_date' => '2026-05-28',
        ]);

        ExpenseReport::factory()->rejected()->create([
            'user_id' => $bob->id,
            'title' => 'Abonnement logiciel - Figma Pro',
            'amount' => 45.00,
            'category' => 'fournitures',
            'description' => 'Renouvellement annuel de la licence Figma. Refusé : non budgété.',
            'expense_date' => '2026-04-03',
        ]);

        ExpenseReport::factory()->create([
            'user_id' => $bob->id,
            'title' => 'Hôtel Ibis - déplacement Lyon',
            'amount' => 95.00,
            'category' => 'hébergement',
            'description' => 'Une nuit à Lyon pour le séminaire commercial.',
            'expense_date' => '2026-05-28',
        ]);
    }
}
