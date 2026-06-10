@extends('layouts.app')

@section('title', 'Todos')

@section('content')
    <header class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-400">Laravel {{ app()->version() }} + Sentry</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-white sm:text-4xl">Modern Todo App</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-400">
                    Manage tasks with intentional exception handling — graceful catches, critical <code class="rounded bg-slate-800 px-1.5 py-0.5 text-indigo-300">report()</code> calls, and uncaught failures auto-sent to Sentry.
                </p>
            </div>
            <div class="flex gap-3 text-center text-xs">
                <div class="rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 backdrop-blur">
                    <div class="text-lg font-semibold text-white">{{ $stats['total'] }}</div>
                    <div class="text-slate-500">Total</div>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 backdrop-blur">
                    <div class="text-lg font-semibold text-emerald-400">{{ $stats['active'] }}</div>
                    <div class="text-slate-500">Active</div>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 backdrop-blur">
                    <div class="text-lg font-semibold text-slate-400">{{ $stats['completed'] }}</div>
                    <div class="text-slate-500">Done</div>
                </div>
            </div>
        </div>
    </header>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="mb-6 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
            {{ session('warning') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl backdrop-blur">
                <h2 class="text-lg font-semibold text-white">Add a todo</h2>
                <form action="{{ route('todos.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="title" class="mb-1.5 block text-sm text-slate-400">Title</label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="e.g. Review pull requests"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-2.5 text-white placeholder:text-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                        >
                        @error('title')
                            <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="description" class="mb-1.5 block text-sm text-slate-400">Description</label>
                        <textarea
                            name="description"
                            id="description"
                            rows="2"
                            placeholder="Optional details..."
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-2.5 text-white placeholder:text-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                        >{{ old('description') }}</textarea>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="priority" class="mb-1.5 block text-sm text-slate-400">Priority</label>
                            <select
                                name="priority"
                                id="priority"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-2.5 text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            >
                                @foreach (\App\Enums\TodoPriority::cases() as $priority)
                                    <option value="{{ $priority->value }}" @selected(old('priority', 'medium') === $priority->value)>
                                        {{ $priority->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="due_at" class="mb-1.5 block text-sm text-slate-400">Due date</label>
                            <input
                                type="datetime-local"
                                name="due_at"
                                id="due_at"
                                value="{{ old('due_at') }}"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-2.5 text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            >
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">
                        Tip: include <code class="text-indigo-300">sync-fail</code> in title to test sync errors, or <code class="text-indigo-300">queue-fail</code> with High priority for queue failures.
                    </p>
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                    >
                        Create todo
                    </button>
                </form>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/70 shadow-xl backdrop-blur">
                <div class="flex items-center justify-between border-b border-slate-800 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Your todos</h2>
                    <div class="flex gap-1 rounded-lg bg-slate-950 p-1 text-xs">
                        <a href="{{ route('todos.index') }}"
                           class="rounded-md px-3 py-1.5 {{ ! $status ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white' }}">
                            All
                        </a>
                        <a href="{{ route('todos.index', ['status' => 'active']) }}"
                           class="rounded-md px-3 py-1.5 {{ $status === 'active' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white' }}">
                            Active
                        </a>
                        <a href="{{ route('todos.index', ['status' => 'completed']) }}"
                           class="rounded-md px-3 py-1.5 {{ $status === 'completed' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white' }}">
                            Done
                        </a>
                    </div>
                </div>

                <ul class="divide-y divide-slate-800">
                    @forelse ($todos as $todo)
                        <li class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-3">
                                <form action="{{ route('todos.toggle', $todo) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="mt-0.5 flex h-5 w-5 items-center justify-center rounded border {{ $todo->is_completed ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400' : 'border-slate-600 hover:border-indigo-500' }}"
                                        aria-label="Toggle complete"
                                    >
                                        @if ($todo->is_completed)
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <div>
                                    <p class="font-medium {{ $todo->is_completed ? 'text-slate-500 line-through' : 'text-white' }}">
                                        {{ $todo->title }}
                                    </p>
                                    @if ($todo->description)
                                        <p class="mt-1 text-sm text-slate-400">{{ $todo->description }}</p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        <span class="rounded-full px-2 py-0.5
                                            @if ($todo->priority === \App\Enums\TodoPriority::High) bg-rose-500/20 text-rose-300
                                            @elseif ($todo->priority === \App\Enums\TodoPriority::Medium) bg-amber-500/20 text-amber-300
                                            @else bg-slate-700 text-slate-300 @endif">
                                            {{ $todo->priority->label() }}
                                        </span>
                                        @if ($todo->due_at)
                                            <span class="text-slate-500">Due {{ $todo->due_at->format('M j, g:i A') }}</span>
                                        @endif
                                        @if ($todo->synced_at)
                                            <span class="text-emerald-500">Synced {{ $todo->synced_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2 sm:shrink-0">
                                <form action="{{ route('todos.sync', $todo) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-slate-300 transition hover:border-indigo-500 hover:text-white">
                                        Sync
                                    </button>
                                </form>
                                <form action="{{ route('todos.destroy', $todo) }}" method="POST" onsubmit="return confirm('Delete this todo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-rose-400 transition hover:border-rose-500 hover:text-rose-300">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-12 text-center text-slate-500">
                            No todos yet. Create one above to get started.
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl backdrop-blur">
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-rose-500"></div>
                    <h2 class="text-lg font-semibold text-white">Sentry Exception Lab</h2>
                </div>
                <p class="mt-2 text-sm text-slate-400">
                    Trigger intentional exceptions to verify Sentry reporting behavior.
                </p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('todos.demo.handled') }}"
                       class="block rounded-xl border border-slate-700 px-4 py-3 text-sm transition hover:border-amber-500/50 hover:bg-amber-500/5">
                        <span class="font-medium text-amber-300">Handled</span>
                        <span class="mt-0.5 block text-xs text-slate-500">Try/catch — user message only, no Sentry</span>
                    </a>
                    <a href="{{ route('todos.demo.reported') }}"
                       class="block rounded-xl border border-slate-700 px-4 py-3 text-sm transition hover:border-rose-500/50 hover:bg-rose-500/5">
                        <span class="font-medium text-rose-300">Reported</span>
                        <span class="mt-0.5 block text-xs text-slate-500">Try/catch + report() — sent to Sentry</span>
                    </a>
                    <a href="{{ route('todos.demo.nested') }}"
                       class="block rounded-xl border border-slate-700 px-4 py-3 text-sm transition hover:border-violet-500/50 hover:bg-violet-500/5">
                        <span class="font-medium text-violet-300">Nested</span>
                        <span class="mt-0.5 block text-xs text-slate-500">Inner report(), outer graceful response</span>
                    </a>
                    <a href="{{ route('todos.demo.uncaught') }}"
                       class="block rounded-xl border border-rose-500/40 bg-rose-500/5 px-4 py-3 text-sm transition hover:bg-rose-500/10">
                        <span class="font-medium text-rose-400">Uncaught</span>
                        <span class="mt-0.5 block text-xs text-slate-500">No catch — Laravel + Sentry auto-capture</span>
                    </a>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 text-sm text-slate-400 shadow-xl backdrop-blur">
                <h3 class="font-semibold text-white">Exception patterns</h3>
                <ul class="mt-3 space-y-2 text-xs leading-relaxed">
                    <li><strong class="text-slate-300">TodoOperationException</strong> — expected errors, handled in UI.</li>
                    <li><strong class="text-slate-300">CriticalTodoException</strong> — always reported via <code class="text-indigo-300">report()</code>.</li>
                    <li><strong class="text-slate-300">Queue::failing</strong> — failed jobs auto-reported.</li>
                    <li><strong class="text-slate-300">Integration::handles</strong> — uncaught exceptions to Sentry.</li>
                </ul>
                <p class="mt-4 text-xs">
                    API: <code class="text-indigo-300">/api/todos</code> · Demo: <code class="text-indigo-300">/api/todos/demo/*</code>
                </p>
            </section>
        </aside>
    </div>
@endsection
