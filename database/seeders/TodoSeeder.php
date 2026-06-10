<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

final class TodoSeeder extends Seeder
{
    public function run(): void
    {
        Todo::factory()->create([
            'title' => 'Set up Sentry monitoring',
            'description' => 'Verify exception reporting with php artisan sentry:test',
            'priority' => 'high',
        ]);

        Todo::factory()->count(4)->create();
        Todo::factory()->completed()->create([
            'title' => 'Ship v1 of the todo app',
        ]);
    }
}
