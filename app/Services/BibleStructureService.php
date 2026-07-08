<?php

namespace App\Services;

use App\Models\BibleBook;
use App\Models\BibleChapter;
use App\Models\BibleVersion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;

class BibleStructureService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function structureForVersion(BibleVersion $version): array
    {
        $config = $this->versionConfig($version->abbreviation);
        $file = ($config['canon'] ?? 'catholic') === 'protestant'
            ? 'books_structure_protestant.json'
            : 'books_structure.json';

        return $this->loadStructureFile($file);
    }

    public function ensureVersionStructure(BibleVersion $version): void
    {
        $structure = $this->structureForVersion($version);
        $localizedNames = $this->localizedBookNames($version);

        foreach ($structure as $bookData) {
            $displayName = $localizedNames[$bookData['name']] ?? $bookData['name'];

            $book = BibleBook::query()->updateOrCreate(
                [
                    'bible_version_id' => $version->id,
                    'book_order' => $bookData['book_order'],
                ],
                [
                    'name' => $displayName,
                    'abbreviation' => $bookData['abbreviation'],
                    'testament' => $bookData['testament'],
                    'chapter_count' => $bookData['chapter_count'],
                ]
            );

            for ($chapterNumber = 1; $chapterNumber <= $bookData['chapter_count']; $chapterNumber++) {
                $chapter = BibleChapter::withTrashed()->firstOrNew([
                    'bible_book_id' => $book->id,
                    'chapter_number' => $chapterNumber,
                ]);

                if ($chapter->trashed()) {
                    $chapter->restore();
                }

                if (! $chapter->exists) {
                    $chapter->verse_count = 0;
                }

                $chapter->save();
            }
        }
    }

    public function ensureAllBookStructures(): void
    {
        foreach (BibleVersion::query()->orderBy('id')->get() as $version) {
            $this->ensureVersionStructure($version);
        }
    }

    public function syncBookChapterCountsFromStructure(): void
    {
        foreach (BibleVersion::query()->orderBy('id')->get() as $version) {
            $booksByOrder = $version->books()->get()->keyBy('book_order');

            foreach ($this->structureForVersion($version) as $bookData) {
                $book = $booksByOrder->get($bookData['book_order']);

                if ($book && (int) $book->chapter_count !== (int) $bookData['chapter_count']) {
                    $book->update(['chapter_count' => $bookData['chapter_count']]);
                }
            }
        }
    }

    public function refreshChapterVerseCounts(): void
    {
        BibleChapter::query()
            ->withCount('verses')
            ->chunkById(200, function ($chapters) {
                foreach ($chapters as $chapter) {
                    if ((int) $chapter->verse_count !== (int) $chapter->verses_count) {
                        $chapter->update(['verse_count' => $chapter->verses_count]);
                    }
                }
            });
    }

    /**
     * @return array<int, array{book: string, chapter: int, expected: int, actual: int, missing_verses: array<int, int>}>
     */
    public function auditVersion(BibleVersion $version, ?BibleVersion $referenceVersion = null): array
    {
        $issues = [];
        $reference = $this->referenceVersionFor($version, $referenceVersion);
        $structure = collect($this->structureForVersion($version))->keyBy('book_order');

        foreach ($version->books()->orderBy('book_order')->get() as $book) {
            $expectedChapters = (int) ($structure->get($book->book_order)['chapter_count'] ?? $book->chapter_count);
            $maxChapter = (int) ($book->chapters()->max('chapter_number') ?? 0);

            if ($maxChapter < $expectedChapters) {
                $issues[] = [
                    'book' => $book->name,
                    'chapter' => 0,
                    'expected' => $expectedChapters,
                    'actual' => $maxChapter,
                    'missing_verses' => [],
                    'issue' => 'missing_chapters',
                ];
            }

            for ($chapterNumber = 1; $chapterNumber <= $expectedChapters; $chapterNumber++) {
                $chapter = $book->chapters()->where('chapter_number', $chapterNumber)->first();

                if (! $chapter) {
                    $issues[] = [
                        'book' => $book->name,
                        'chapter' => $chapterNumber,
                        'expected' => 0,
                        'actual' => 0,
                        'missing_verses' => [],
                        'issue' => 'chapter_missing',
                    ];

                    continue;
                }

                $actualVerses = $chapter->verses()->orderBy('verse_number')->pluck('verse_number')->all();

                if ($actualVerses === []) {
                    $issues[] = [
                        'book' => $book->name,
                        'chapter' => $chapterNumber,
                        'expected' => $reference ? $this->referenceVerseCount($reference, $book, $chapterNumber) : 0,
                        'actual' => 0,
                        'missing_verses' => [],
                        'issue' => 'empty_chapter',
                    ];

                    continue;
                }

                if (! $reference) {
                    continue;
                }

                $expectedVerseCount = $this->referenceVerseCount($reference, $book, $chapterNumber);

                if ($expectedVerseCount > 0 && count($actualVerses) < $expectedVerseCount) {
                    $missing = array_values(array_diff(range(1, $expectedVerseCount), $actualVerses));
                    $issues[] = [
                        'book' => $book->name,
                        'chapter' => $chapterNumber,
                        'expected' => $expectedVerseCount,
                        'actual' => count($actualVerses),
                        'missing_verses' => $missing,
                        'issue' => 'missing_verses',
                    ];
                }
            }
        }

        return $issues;
    }

    protected function referenceVersionFor(BibleVersion $version, ?BibleVersion $referenceVersion): ?BibleVersion
    {
        if ($referenceVersion && $this->canCompareToReference($version, $referenceVersion)) {
            return $referenceVersion;
        }

        if ($this->canCompareToReference($version, BibleVersion::query()->where('abbreviation', 'KJV')->first())) {
            return BibleVersion::query()->where('abbreviation', 'KJV')->first();
        }

        return null;
    }

    protected function canCompareToReference(BibleVersion $version, ?BibleVersion $reference): bool
    {
        if (! $reference || $version->id === $reference->id) {
            return false;
        }

        $versionConfig = $this->versionConfig($version->abbreviation);
        $referenceConfig = $this->versionConfig($reference->abbreviation);

        if (($versionConfig['canon'] ?? '') !== ($referenceConfig['canon'] ?? '')) {
            return false;
        }

        if (($versionConfig['language'] ?? '') !== ($referenceConfig['language'] ?? '')) {
            return false;
        }

        $referenceStrategy = $referenceConfig['import']['strategy'] ?? '';
        $versionStrategy = $versionConfig['import']['strategy'] ?? '';

        return in_array($referenceStrategy, ['thiagobodruk', 'thiagobodruk_deuterocanonical'], true)
            && in_array($versionStrategy, ['thiagobodruk', 'thiagobodruk_deuterocanonical', 'douay'], true);
    }

    protected function referenceVerseCount(?BibleVersion $reference, BibleBook $book, int $chapterNumber): int
    {
        if (! $reference) {
            return 0;
        }

        $referenceBook = $reference->books()->where('book_order', $book->book_order)->first();

        if (! $referenceBook) {
            return 0;
        }

        $chapter = $referenceBook->chapters()->where('chapter_number', $chapterNumber)->first();

        return $chapter ? (int) $chapter->verse_count : 0;
    }

    /**
     * @return array<string, string>
     */
    protected function localizedBookNames(BibleVersion $version): array
    {
        $config = $this->versionConfig($version->abbreviation);
        $localeKey = $config['book_names'] ?? null;

        if (! $localeKey) {
            return [];
        }

        $path = database_path('seeders/data/bible/book_name_locales.json');
        $payload = json_decode(File::get($path), true);

        return is_array($payload[$localeKey] ?? null) ? $payload[$localeKey] : [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function versionConfig(string $abbreviation): array
    {
        return $this->versionConfigs()
            ->get(strtoupper($abbreviation))
            ?? throw new RuntimeException("Bible version config not found for [{$abbreviation}].");
    }

    /**
     * @return Collection<string, array<string, mixed>>
     */
    protected function versionConfigs(): Collection
    {
        static $configs = null;

        if ($configs === null) {
            $path = database_path('seeders/data/bible/versions.json');
            $payload = json_decode(File::get($path), true);

            if (! is_array($payload)) {
                throw new RuntimeException('Invalid Bible versions configuration.');
            }

            $configs = collect($payload)->keyBy(
                fn (array $config) => strtoupper($config['abbreviation'])
            );
        }

        return $configs;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function loadStructureFile(string $filename): array
    {
        $path = database_path('seeders/data/bible/'.$filename);
        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            throw new RuntimeException("Invalid Bible structure file at {$path}.");
        }

        return $payload;
    }
}
