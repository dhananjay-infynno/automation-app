<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\CriticalTodoException;
use App\Exceptions\TodoOperationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use RuntimeException;
use Throwable;

/**
 * Intentional exception scenarios for demonstrating Sentry integration.
 */
final class TodoDemoController extends Controller
{
    /**
     * Handled gracefully — caught, user-friendly message, NOT sent to Sentry.
     */
    public function handled(): RedirectResponse|JsonResponse
    {
        try {
            throw TodoOperationException::duplicateTitle('Buy groceries');
        } catch (TodoOperationException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'scenario' => 'handled',
                    'message' => $e->getMessage(),
                    'sentry' => false,
                ]);
            }

            return back()->with(
                'warning',
                "[Handled] {$e->getMessage()} — not reported to Sentry.",
            );
        }
    }

    /**
     * Caught but reported — try/catch with report($e), sent to Sentry.
     */
    public function reported(): RedirectResponse|JsonResponse
    {
        try {
            throw CriticalTodoException::syncFailed(42, 'Simulated API timeout after 30s');
        } catch (CriticalTodoException $e) {
            report($e);

            if (request()->expectsJson()) {
                return response()->json([
                    'scenario' => 'reported',
                    'message' => $e->getMessage(),
                    'sentry' => true,
                ], 500);
            }

            return back()->with(
                'error',
                '[Reported] Critical error caught and sent to Sentry via report().',
            );
        }
    }

    /**
     * Uncaught — bubbles to Sentry Integration::handles(), auto-captured.
     */
    public function uncaught(): never
    {
        throw new RuntimeException('Simulated uncaught critical failure — auto-reported to Sentry');
    }

    /**
     * Nested try/catch — inner reports, outer returns graceful response.
     */
    public function nested(): RedirectResponse|JsonResponse
    {
        try {
            try {
                throw new RuntimeException('Inner service layer failure');
            } catch (Throwable $inner) {
                report($inner);

                throw CriticalTodoException::dataCorruption(99);
            }
        } catch (CriticalTodoException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'scenario' => 'nested',
                    'message' => $e->getMessage(),
                    'sentry' => true,
                    'note' => 'Inner exception reported; outer provides user message.',
                ], 500);
            }

            return back()->with(
                'error',
                '[Nested] Inner error reported to Sentry; user sees safe message.',
            );
        }
    }

    /**
     * Auto-fix pipeline test — handled without uncaught exception.
     */
    public function uniqueIssue(): RedirectResponse|JsonResponse
    {
        if (request()->expectsJson()) {
            return response()->json([
                'scenario' => 'unique-issue',
                'message' => 'Auto-fix pipeline test resolved successfully',
                'sentry' => false,
            ]);
        }

        return back()->with(
            'success',
            '[Unique Issue] Auto-fix pipeline test resolved — not reported to Sentry.',
        );
    }
}
