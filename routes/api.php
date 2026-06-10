<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\TodoDemoController;
use Illuminate\Support\Facades\Route;

Route::prefix('todos')->group(function (): void {
    Route::get('/', [TodoController::class, 'apiIndex']);
    Route::post('/', [TodoController::class, 'apiStore']);

    Route::prefix('demo')->group(function (): void {
        Route::get('/handled', [TodoDemoController::class, 'handled']);
        Route::get('/reported', [TodoDemoController::class, 'reported']);
        Route::get('/uncaught', [TodoDemoController::class, 'uncaught']);
        Route::get('/nested', [TodoDemoController::class, 'nested']);
    });
});
