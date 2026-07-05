<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\BibleVersion;
use Illuminate\Validation\Rule;

class BibleVersionController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return BibleVersion::class;
    }

    protected function searchableFields(): array
    {
        return ['name', 'abbreviation', 'language', 'description'];
    }

    protected function filterableFields(): array
    {
        return ['is_active', 'language'];
    }

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:50', 'unique:bible_versions,abbreviation'],
            'language' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'abbreviation' => ['sometimes', 'string', 'max:50', Rule::unique('bible_versions', 'abbreviation')->ignore($id)],
            'language' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    protected function importUniqueKeys(): array
    {
        return ['abbreviation'];
    }
}
