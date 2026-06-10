<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\CriticalTodoException;
use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class ProcessTodoReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        private readonly Todo $todo,
    ) {}

    public function handle(): void
    {
        if (str_contains(strtolower($this->todo->title), 'queue-fail')) {
            throw CriticalTodoException::syncFailed(
                $this->todo->id,
                'Reminder delivery service unavailable',
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }
}
