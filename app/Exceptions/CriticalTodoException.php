<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Critical failures that must be reported to Sentry via report().
 */
final class CriticalTodoException extends RuntimeException
{
    public static function syncFailed(int $todoId, string $reason): self
    {
        return new self("Critical todo sync failed for #{$todoId}: {$reason}");
    }

    public static function dataCorruption(int $todoId): self
    {
        return new self("Todo data integrity check failed for #{$todoId}");
    }
}
