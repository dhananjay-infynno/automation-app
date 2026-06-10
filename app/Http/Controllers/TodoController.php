<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\CriticalTodoException;
use App\Exceptions\TodoOperationException;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TodoController extends Controller
{
    public function __construct(
        private readonly TodoService $todoService,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString() ?: null;

        return view('todos.index', [
            'todos' => $this->todoService->list($status),
            'status' => $status,
            'stats' => [
                'total' => Todo::query()->count(),
                'active' => Todo::query()->active()->count(),
                'completed' => Todo::query()->completed()->count(),
            ],
        ]);
    }

    public function store(StoreTodoRequest $request): RedirectResponse
    {
        try {
            $this->todoService->create($request->validated());

            return back()->with('success', 'Todo created successfully.');
        } catch (TodoOperationException $e) {
            return back()
                ->withInput()
                ->with('warning', $e->getMessage());
        } catch (CriticalTodoException $e) {
            return back()
                ->withInput()
                ->with('error', 'A critical error occurred. Our team has been notified.');
        }
    }

    public function toggle(Todo $todo): RedirectResponse
    {
        try {
            $this->todoService->toggle($todo);

            return back()->with('success', 'Todo updated.');
        } catch (CriticalTodoException $e) {
            return back()->with('error', 'Could not update todo. Our team has been notified.');
        }
    }

    public function destroy(Todo $todo): RedirectResponse
    {
        try {
            $this->todoService->delete($todo);

            return back()->with('success', 'Todo deleted.');
        } catch (CriticalTodoException $e) {
            return back()->with('error', 'Could not delete todo. Our team has been notified.');
        }
    }

    public function sync(Todo $todo): RedirectResponse
    {
        try {
            $this->todoService->sync($todo);

            return back()->with('success', 'Todo synced with external calendar.');
        } catch (CriticalTodoException $e) {
            return back()->with('error', 'Sync failed. Our team has been notified via Sentry.');
        }
    }

    public function apiIndex(Request $request): JsonResponse
    {
        $status = $request->string('status')->toString() ?: null;

        return TodoResource::collection($this->todoService->list($status))
            ->response();
    }

    public function apiStore(StoreTodoRequest $request): JsonResponse
    {
        try {
            $todo = $this->todoService->create($request->validated());

            return (new TodoResource($todo))
                ->response()
                ->setStatusCode(201);
        } catch (TodoOperationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (CriticalTodoException $e) {
            return response()->json(['message' => 'Critical error. Team notified.'], 500);
        }
    }
}
