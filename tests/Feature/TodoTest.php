<?php

declare(strict_types=1);

use App\Models\Todo;

it('displays the todo index page', function (): void {
    Todo::factory()->create(['title' => 'Test task']);

    $this->get('/todos')
        ->assertOk()
        ->assertSee('Test task')
        ->assertSee('Sentry Exception Lab');
});

it('creates a todo', function (): void {
    $this->post('/todos', [
        'title' => 'New task',
        'priority' => 'medium',
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Todo::query()->where('title', 'New task')->exists())->toBeTrue();
});

it('handles duplicate title gracefully without error', function (): void {
    Todo::factory()->create(['title' => 'Duplicate']);

    $this->post('/todos', ['title' => 'Duplicate'])
        ->assertRedirect()
        ->assertSessionHas('warning');
});

it('returns handled demo as json', function (): void {
    $this->getJson('/api/todos/demo/handled')
        ->assertOk()
        ->assertJsonPath('sentry', false);
});

it('returns reported demo with sentry flag', function (): void {
    $this->getJson('/api/todos/demo/reported')
        ->assertStatus(500)
        ->assertJsonPath('sentry', true);
});
