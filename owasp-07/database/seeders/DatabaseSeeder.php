<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $maintenance = User::factory()->create([
            'name' => 'Compte Maintenance',
            'email' => 'maintenance@corp.local',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        $alice = User::factory()->create([
            'name' => 'Alice Moreau',
            'email' => 'alice@corp.local',
            'password' => Hash::make('alice123'),
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Bob Durand',
            'email' => 'bob@corp.local',
            'password' => Hash::make('Tr0ub4dor&3'),
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Admin Système',
            'email' => 'admin@corp.local',
            'password' => Hash::make('K#9mP2$xL7vQ'),
            'role' => 'admin',
        ]);

        Announcement::factory()->create([
            'user_id' => $maintenance->id,
            'title' => 'Maintenance planifiée — samedi 25 janvier',
            'content' => 'Une interruption de service est prévue le samedi 25 janvier de 2h à 6h pour la mise à jour des serveurs. Pensez à sauvegarder vos travaux en cours avant vendredi soir.',
            'category' => 'urgent',
        ]);

        Announcement::factory()->create([
            'user_id' => $alice->id,
            'title' => 'Rappel : entretiens annuels Q1 2025',
            'content' => 'Les entretiens annuels du premier trimestre se dérouleront du 10 au 28 février. Merci de contacter votre responsable pour planifier votre créneau avant le 31 janvier.',
            'category' => 'rh',
        ]);

        Announcement::factory()->create([
            'user_id' => $maintenance->id,
            'title' => 'Bienvenue sur CorpHub',
            'content' => "CorpHub est votre nouvel intranet d'entreprise. Retrouvez ici les annonces, l'annuaire des collaborateurs et l'ensemble des ressources internes.",
            'category' => 'info',
        ]);
    }
}
