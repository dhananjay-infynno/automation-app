<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\TodoDemoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/todos');

Route::prefix('todos')->name('todos.')->group(function (): void {
    Route::get('/', [TodoController::class, 'index'])->name('index');
    Route::post('/', [TodoController::class, 'store'])->name('store');
    Route::patch('/{todo}/toggle', [TodoController::class, 'toggle'])->name('toggle');
    Route::delete('/{todo}', [TodoController::class, 'destroy'])->name('destroy');
    Route::post('/{todo}/sync', [TodoController::class, 'sync'])->name('sync');

    Route::prefix('demo')->name('demo.')->group(function (): void {
        Route::get('/handled', [TodoDemoController::class, 'handled'])->name('handled');
        Route::get('/reported', [TodoDemoController::class, 'reported'])->name('reported');
        Route::get('/uncaught', [TodoDemoController::class, 'uncaught'])->name('uncaught');
        Route::get('/nested', [TodoDemoController::class, 'nested'])->name('nested');
        Route::get('/unique-issue', [TodoDemoController::class, 'uniqueIssue'])->name('unique-issue');
    });
});
