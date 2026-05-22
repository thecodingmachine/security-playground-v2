<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    private static int $counter = 0;

    public function definition(): array
    {
        self::$counter++;

        return [
            'user_id' => User::factory(),
            'number' => 'FAC-2025-'.str_pad((string) self::$counter, 3, '0', STR_PAD_LEFT),
            'client_name' => fake()->company(),
            'client_email' => fake()->companyEmail(),
            'amount' => fake()->randomFloat(2, 500, 15000),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'overdue']),
            'issued_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'due_at' => fake()->dateTimeBetween('now', '+2 months'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
