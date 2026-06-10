<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TodoPriority;
use App\Exceptions\CriticalTodoException;
use App\Exceptions\TodoOperationException;
use App\Jobs\ProcessTodoReminder;
use App\Models\Todo;
use Illuminate\Support\Collection;
use Throwable;

final class TodoService
{
    public function __construct(
        private readonly TodoSyncService $syncService,
    ) {}

    public function list(?string $status = null): Collection
    {
        return Todo::query()
            ->filter($status)
            ->latest()
            ->get();
    }

    /**
     * @param  array{title: string, description?: string|null, priority?: string, due_at?: string|null}  $data
     */
    public function create(array $data): Todo
    {
        try {
            if (Todo::query()->where('title', $data['title'])->exists()) {
                throw TodoOperationException::duplicateTitle($data['title']);
            }

            $todo = Todo::query()->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? TodoPriority::Medium->value,
                'due_at' => $data['due_at'] ?? null,
            ]);

            if ($todo->priority === TodoPriority::High) {
                ProcessTodoReminder::dispatch($todo);
            }

            return $todo;
        } catch (TodoOperationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            throw CriticalTodoException::dataCorruption(0);
        }
    }

    public function toggle(Todo $todo): Todo
    {
        try {
            $todo->update(['is_completed' => ! $todo->is_completed]);

            return $todo->refresh();
        } catch (Throwable $e) {
            report($e);

            throw CriticalTodoException::dataCorruption($todo->id);
        }
    }

    public function delete(Todo $todo): void
    {
        try {
            $todo->delete();
        } catch (Throwable $e) {
            report($e);

            throw CriticalTodoException::dataCorruption($todo->id);
        }
    }

    public function sync(Todo $todo): Todo
    {
        try {
            return $this->syncService->sync($todo);
        } catch (CriticalTodoException $e) {
            report($e);

            throw $e;
        } catch (Throwable $e) {
            report($e);

            throw CriticalTodoException::syncFailed($todo->id, $e->getMessage());
        }
    }
}
