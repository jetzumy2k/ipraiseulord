<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\BibleChapter;

class BibleChapterController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return BibleChapter::class;
    }

    protected function defaultRelations(): array
    {
        return ['book.version'];
    }

    protected function filterableFields(): array
    {
        return ['bible_book_id'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'chapter_number', 'created_at', 'updated_at'];
    }

    protected function storeRules(): array
    {
        return [
            'bible_book_id' => ['required', 'exists:bible_books,id'],
            'chapter_number' => ['required', 'integer', 'min:1'],
            'verse_count' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'bible_book_id' => ['sometimes', 'exists:bible_books,id'],
            'chapter_number' => ['sometimes', 'integer', 'min:1'],
            'verse_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
