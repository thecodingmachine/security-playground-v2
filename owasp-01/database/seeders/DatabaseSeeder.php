<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->prepareDocumentsDirectory();

        $alice = User::factory()->create([
            'name' => 'Alice Martin',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $bob = User::factory()->create([
            'name' => 'Bob Dupont',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Admin Système',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Alice : factures IDs 1–5
        $this->seedInvoicesForUser($alice, 1);

        // Bob : factures IDs 6–10
        $this->seedInvoicesForUser($bob, 6);
    }

    private function seedInvoicesForUser(User $user, int $startIndex): void
    {
        $clients = [
            ['name' => 'TechCorp SAS', 'email' => 'compta@techcorp.fr'],
            ['name' => 'Innovate Labs', 'email' => 'billing@innovatelabs.io'],
            ['name' => 'Acme Solutions', 'email' => 'finance@acmesolutions.com'],
            ['name' => 'Digital Agency Pro', 'email' => 'invoice@digitalagencypro.fr'],
            ['name' => 'StartUp Boost', 'email' => 'accounts@startupboost.co'],
        ];

        $statuses = ['paid', 'sent', 'overdue', 'draft', 'paid'];
        $amounts = [4250.00, 8900.00, 1375.50, 12000.00, 3680.00];

        foreach ($clients as $index => $client) {
            $num = str_pad((string) ($startIndex + $index), 3, '0', STR_PAD_LEFT);
            $number = 'FAC-2025-'.$num;

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'number' => $number,
                'client_name' => $client['name'],
                'client_email' => $client['email'],
                'amount' => $amounts[$index],
                'status' => $statuses[$index],
                'issued_at' => now()->subDays(30 + $index * 15),
                'due_at' => now()->addDays(30 - $index * 5),
                'notes' => $index === 1 ? 'Prestation de développement - Sprint 3' : null,
            ]);

            // Les 3 premières factures de chaque utilisateur ont un document attaché
            if ($index < 3) {
                $filename = 'invoice_'.$number.'.pdf';
                $this->createFakeDocument($filename, $number);

                Document::create([
                    'invoice_id' => $invoice->id,
                    'original_name' => $number.'.pdf',
                    'filename' => $filename,
                    'size' => 42000 + $index * 1000,
                ]);
            }
        }
    }

    private function prepareDocumentsDirectory(): void
    {
        $dir = storage_path('app/documents');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function createFakeDocument(string $filename, string $invoiceNumber): void
    {
        $path = storage_path('app/documents/'.$filename);
        $content = '%PDF-1.4'.PHP_EOL
            .'% Facture '.$invoiceNumber.PHP_EOL
            .'% Ce document est un fichier de démonstration généré automatiquement.'.PHP_EOL;
        file_put_contents($path, $content);
    }
}
