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
