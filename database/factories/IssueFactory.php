<?php

namespace Database\Factories;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(2),
            'priority' => fake()->randomElement(IssuePriority::cases()),
            'category' => fake()->randomElement(IssueCategory::cases()),
            'status' => IssueStatus::OPEN,
            'summary' => null,
            'suggested_action' => null,
            'escalated_at' => null,
        ];
    }

    public function escalated(): static
    {
        return $this->state(fn(array $attributes) => [
            'escalated_at' => now(),
        ]);
    }

    public function withSummary(): static
    {
        return $this->state(fn(array $attributes) => [
            'summary' => 'Generated summary: ' . fake()->sentence(),
            'suggested_action' => 'Generated action: ' . fake()->sentence(),
        ]);
    }
}
