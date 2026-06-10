<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TodoPriority;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Todo>
 */
final class TodoFactory extends Factory
{
    protected $model = Todo::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'is_completed' => false,
            'priority' => fake()->randomElement(TodoPriority::cases()),
            'due_at' => fake()->optional()->dateTimeBetween('now', '+2 weeks'),
            'synced_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => ['is_completed' => true]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (): array => ['priority' => TodoPriority::High]);
    }
}
