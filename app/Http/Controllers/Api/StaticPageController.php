<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\StaticPage;

class StaticPageController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return StaticPage::class;
    }

    protected function searchableFields(): array
    {
        return ['slug', 'title', 'content', 'meta_description'];
    }

    protected function filterableFields(): array
    {
        return ['is_published'];
    }

    protected function storeRules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'unique:static_pages,slug'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'slug' => ['sometimes', 'string', 'max:255', 'unique:static_pages,slug,'.$id],
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ];
    }

    protected function importUniqueKeys(): array
    {
        return ['slug'];
    }
}
