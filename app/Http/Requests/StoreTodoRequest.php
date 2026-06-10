<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TodoPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['nullable', Rule::enum(TodoPriority::class)],
            'due_at' => ['nullable', 'date'],
        ];
    }
}
