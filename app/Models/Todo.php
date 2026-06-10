<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TodoPriority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_completed',
        'priority',
        'due_at',
        'synced_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'priority' => TodoPriority::class,
        'due_at' => 'immutable_datetime',
        'synced_at' => 'immutable_datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    public function scopeFilter(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            'active' => $query->active(),
            'completed' => $query->completed(),
            default => $query,
        };
    }
}
