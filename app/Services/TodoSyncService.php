<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CriticalTodoException;
use App\Models\Todo;

final class TodoSyncService
{
    /**
     * Simulates an external sync API. Fails intentionally when title contains "sync-fail".
     */
    public function sync(Todo $todo): Todo
    {
        if (str_contains(strtolower($todo->title), 'sync-fail')) {
            throw CriticalTodoException::syncFailed(
                $todo->id,
                'External calendar API returned HTTP 503',
            );
        }

        $todo->update(['synced_at' => now()]);

        return $todo->refresh();
    }
}
