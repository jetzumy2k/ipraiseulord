<?php

namespace App\Services;

use App\Models\BibleVerse;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class RandomBibleVerseService
{
    public const WIDGET_LANGUAGE = 'en';

    /**
     * @return array<string, mixed>|null
     */
    public function randomAny(): ?array
    {
        $scopes = ['psalms', 'old-testament', 'new-testament'];
        shuffle($scopes);

        foreach ($scopes as $scope) {
            $verse = $this->random($scope);

            if ($verse) {
                return $verse;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function random(string $scope): ?array
    {
        $query = $this->baseQuery();

        match ($scope) {
            'psalms' => $query->whereHas(
                'chapter.book',
                fn ($builder) => $builder->where('name', 'Psalms')
            ),
            'old-testament' => $query->whereHas(
                'chapter.book',
                fn ($builder) => $builder
                    ->where('testament', 'old')
                    ->where('name', '!=', 'Psalms')
            ),
            'new-testament' => $query->whereHas(
                'chapter.book',
                fn ($builder) => $builder->where('testament', 'new')
            ),
            default => throw new InvalidArgumentException("Unsupported verse scope: {$scope}"),
        };

        $verse = $query
            ->whereNotNull('text')
            ->where('text', '!=', '')
            ->inRandomOrder()
            ->first();

        if (! $verse) {
            return null;
        }

        return $this->formatVerse($verse);
    }

    protected function baseQuery(): Builder
    {
        return BibleVerse::query()
            ->with(['chapter.book.version'])
            ->whereHas('chapter.book.version', fn ($builder) => $builder
                ->where('is_active', true)
                ->where('language', self::WIDGET_LANGUAGE));
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatVerse(BibleVerse $verse): array
    {
        $book = $verse->chapter?->book;
        $chapterNumber = $verse->chapter?->chapter_number ?? 0;

        return [
            'reference' => sprintf(
                '%s %d:%d',
                $book?->name ?? 'Scripture',
                $chapterNumber,
                $verse->verse_number
            ),
            'text' => $verse->text,
            'book' => $book?->name,
            'chapter' => $chapterNumber,
            'verse' => $verse->verse_number,
            'version' => $book?->version?->abbreviation,
            'language' => $book?->version?->language ?? self::WIDGET_LANGUAGE,
        ];
    }
}
