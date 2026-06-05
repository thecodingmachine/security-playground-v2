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
