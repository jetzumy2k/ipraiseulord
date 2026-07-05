<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\BibleVerse;

class BibleVerseController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return BibleVerse::class;
    }

    protected function defaultRelations(): array
    {
        return ['chapter.book.version'];
    }

    protected function searchableFields(): array
    {
        return ['text'];
    }

    protected function filterableFields(): array
    {
        return ['bible_chapter_id'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'verse_number', 'created_at', 'updated_at'];
    }

    protected function storeRules(): array
    {
        return [
            'bible_chapter_id' => ['required', 'exists:bible_chapters,id'],
            'verse_number' => ['required', 'integer', 'min:1'],
            'text' => ['required', 'string'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'bible_chapter_id' => ['sometimes', 'exists:bible_chapters,id'],
            'verse_number' => ['sometimes', 'integer', 'min:1'],
            'text' => ['sometimes', 'string'],
        ];
    }
}
