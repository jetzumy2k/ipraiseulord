<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\PageVisit;

class PageVisitController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return PageVisit::class;
    }

    protected function searchableFields(): array
    {
        return ['visitor_id', 'page_type', 'page_slug', 'page_title', 'ip_address'];
    }

    protected function filterableFields(): array
    {
        return ['page_type', 'visitor_id'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'visited_at', 'created_at'];
    }

    protected function storeRules(): array
    {
        return [
            'visitor_id' => ['nullable', 'string', 'max:255'],
            'page_type' => ['required', 'string', 'max:255'],
            'page_id' => ['nullable', 'integer'],
            'page_slug' => ['nullable', 'string', 'max:255'],
            'page_title' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'user_agent' => ['nullable', 'string', 'max:1024'],
            'visited_at' => ['nullable', 'date'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'visitor_id' => ['nullable', 'string', 'max:255'],
            'page_type' => ['sometimes', 'string', 'max:255'],
            'page_id' => ['nullable', 'integer'],
            'page_slug' => ['nullable', 'string', 'max:255'],
            'page_title' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'user_agent' => ['nullable', 'string', 'max:1024'],
            'visited_at' => ['nullable', 'date'],
        ];
    }
}
