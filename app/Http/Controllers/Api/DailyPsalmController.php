<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\DailyPsalm;

class DailyPsalmController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return DailyPsalm::class;
    }

    protected function searchableFields(): array
    {
        return ['title', 'text', 'reference'];
    }

    protected function filterableFields(): array
    {
        return ['is_active', 'psalm_number'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'psalm_number', 'title', 'created_at', 'updated_at'];
    }

    protected function storeRules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'psalm_number' => ['nullable', 'integer', 'min:1'],
            'text' => ['required', 'string'],
            'reference' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'psalm_number' => ['nullable', 'integer', 'min:1'],
            'text' => ['sometimes', 'string'],
            'reference' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }
}
