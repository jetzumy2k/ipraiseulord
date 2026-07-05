<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\Proverb;

class ProverbController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return Proverb::class;
    }

    protected function searchableFields(): array
    {
        return ['text', 'reference', 'bible_version'];
    }

    protected function filterableFields(): array
    {
        return ['is_active', 'bible_version'];
    }

    protected function storeRules(): array
    {
        return [
            'text' => ['required', 'string'],
            'reference' => ['nullable', 'string', 'max:255'],
            'bible_version' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'text' => ['sometimes', 'string'],
            'reference' => ['nullable', 'string', 'max:255'],
            'bible_version' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }
}
