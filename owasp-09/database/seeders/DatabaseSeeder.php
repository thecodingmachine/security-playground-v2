<?php

namespace Database\Seeders;

use App\Models\Transfer;
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
            'name' => 'Alice Dupont',
            'email' => 'alice@bank.local',
            'password' => Hash::make('alice123'),
            'role' => 'user',
            'balance' => 2425.50,
        ]);

        $bob = User::factory()->create([
            'name' => 'Bob Martin',
            'email' => 'bob@bank.local',
            'password' => Hash::make('password'),
            'role' => 'user',
            'balance' => 1874.50,
        ]);

        User::factory()->create([
            'name' => 'Admin Système',
            'email' => 'admin@bank.local',
            'password' => Hash::make('4dm1n_S3cur3!'),
            'role' => 'admin',
            'balance' => 0.00,
        ]);

        Transfer::query()->create([
            'sender_id' => $alice->id,
            'recipient_id' => $bob->id,
            'amount' => 150.00,
            'note' => 'Remboursement déjeuner',
        ]);

        Transfer::query()->create([
            'sender_id' => $bob->id,
            'recipient_id' => $alice->id,
            'amount' => 75.50,
            'note' => 'Part cinéma',
        ]);
    }
}
