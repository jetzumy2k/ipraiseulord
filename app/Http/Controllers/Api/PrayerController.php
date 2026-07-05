<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\Prayer;
use Illuminate\Validation\Rule;

class PrayerController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return Prayer::class;
    }

    protected function searchableFields(): array
    {
        return ['title', 'slug', 'content', 'description'];
    }

    protected function filterableFields(): array
    {
        return ['category', 'is_active'];
    }

    protected function storeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:prayers,slug'],
            'category' => ['nullable', Rule::in(['regular', 'common', 'rosary', 'liturgy', 'other'])],
            'content' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('prayers', 'slug')->ignore($id)],
            'category' => ['nullable', Rule::in(['regular', 'common', 'rosary', 'liturgy', 'other'])],
            'content' => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }

    protected function importUniqueKeys(): array
    {
        return ['slug'];
    }
}
