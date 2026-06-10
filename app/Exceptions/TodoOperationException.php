<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Expected operational failures — handled gracefully, not sent to Sentry.
 */
final class TodoOperationException extends RuntimeException
{
    public static function duplicateTitle(string $title): self
    {
        return new self("A todo titled \"{$title}\" already exists.");
    }

    public static function invalidTransition(string $from, string $to): self
    {
        return new self("Cannot transition todo from {$from} to {$to}.");
    }
}
