<?php

namespace App\Services;

use App\Models\BibleBook;
use App\Models\BibleVerse;
use App\Models\BibleVersion;
use Illuminate\Support\Collection;

class BiblePassageResolver
{
    /** @var array<string, string> */
    protected array $bookAliases = [
        'Song of Solomon' => 'Song of Songs',
        'Canticle of Canticles' => 'Song of Songs',
        'Apocalypse' => 'Revelation',
        'Apocalypse of Saint John' => 'Revelation',
        '1 Machabees' => '1 Maccabees',
        '2 Machabees' => '2 Maccabees',
        'Ecclesiasticus' => 'Sirach',
        '1 Corinthians' => '1 Corinthians',
        '2 Corinthians' => '2 Corinthians',
        '1 Thessalonians' => '1 Thessalonians',
        '2 Thessalonians' => '2 Thessalonians',
        '1 Timothy' => '1 Timothy',
        '2 Timothy' => '2 Timothy',
        '1 Peter' => '1 Peter',
        '2 Peter' => '2 Peter',
        '1 John' => '1 John',
        '2 John' => '2 John',
        '3 John' => '3 John',
    ];

    /** @var array<string, string> */
    protected array $bookAbbreviations = [
        'Genesis' => 'Gen',
        'Exodus' => 'Exod',
        'Leviticus' => 'Lev',
        'Numbers' => 'Num',
        'Deuteronomy' => 'Deut',
        'Joshua' => 'Josh',
        'Judges' => 'Judg',
        'Ruth' => 'Ruth',
        '1 Samuel' => '1 Sam',
        '2 Samuel' => '2 Sam',
        '1 Kings' => '1 Kgs',
        '2 Kings' => '2 Kgs',
        '1 Chronicles' => '1 Chr',
        '2 Chronicles' => '2 Chr',
        'Ezra' => 'Ezra',
        'Nehemiah' => 'Neh',
        'Tobit' => 'Tob',
        'Judith' => 'Jdt',
        'Esther' => 'Esth',
        '1 Maccabees' => '1 Macc',
        '2 Maccabees' => '2 Macc',
        'Job' => 'Job',
        'Psalms' => 'Ps',
        'Psalm' => 'Ps',
        'Proverbs' => 'Prov',
        'Ecclesiastes' => 'Eccl',
        'Song of Songs' => 'Song',
        'Wisdom' => 'Wis',
        'Sirach' => 'Sir',
        'Isaiah' => 'Isa',
        'Jeremiah' => 'Jer',
        'Lamentations' => 'Lam',
        'Baruch' => 'Bar',
        'Ezekiel' => 'Ezek',
        'Daniel' => 'Dan',
        'Hosea' => 'Hos',
        'Joel' => 'Joel',
        'Amos' => 'Amos',
        'Obadiah' => 'Obad',
        'Jonah' => 'Jonah',
        'Micah' => 'Mic',
        'Nahum' => 'Nah',
        'Habakkuk' => 'Hab',
        'Zephaniah' => 'Zeph',
        'Haggai' => 'Hag',
        'Zechariah' => 'Zech',
        'Malachi' => 'Mal',
        'Matthew' => 'Matt',
        'Mark' => 'Mark',
        'Luke' => 'Luke',
        'John' => 'John',
        'Acts' => 'Acts',
        'Romans' => 'Rom',
        '1 Corinthians' => '1 Cor',
        '2 Corinthians' => '2 Cor',
        'Galatians' => 'Gal',
        'Ephesians' => 'Eph',
        'Philippians' => 'Phil',
        'Colossians' => 'Col',
        '1 Thessalonians' => '1 Thess',
        '2 Thessalonians' => '2 Thess',
        '1 Timothy' => '1 Tim',
        '2 Timothy' => '2 Tim',
        'Titus' => 'Titus',
        'Philemon' => 'Phlm',
        'Hebrews' => 'Heb',
        'James' => 'Jas',
        '1 Peter' => '1 Pet',
        '2 Peter' => '2 Pet',
        '1 John' => '1 John',
        '2 John' => '2 John',
        '3 John' => '3 John',
        'Jude' => 'Jude',
        'Revelation' => 'Rev',
    ];

    /** @var array<int, array<string, BibleBook>>|null */
    protected ?array $booksByVersion = null;

    public function buildUrl(string $reference, string $versionAbbrev = 'NABRE'): ?string
    {
        $reference = trim($reference);

        if ($reference === '') {
            return null;
        }

        if (preg_match('/^psalm\s+/i', $reference)) {
            return $this->buildPsalmUrl($reference, $versionAbbrev);
        }

        if (! preg_match('/^(.+?)\s+(\d+)\s*:\s*(.+)$/u', $reference, $matches)) {
            return null;
        }

        $book = trim($matches[1]);
        $chapter = (int) $matches[2];
        $firstVerse = $this->firstVerseFromPart(trim($matches[3]));

        return $this->buildUrlFromParts($this->bookAliases[$book] ?? $book, $chapter, $firstVerse, $versionAbbrev);
    }

    public function resolve(string $reference, string $versionAbbrev = 'RSVCE'): ?string
    {
        $reference = trim($reference);

        if ($reference === '') {
            return null;
        }

        if (preg_match('/^psalm\s+/i', $reference)) {
            return $this->resolvePsalmPassage($reference, $versionAbbrev);
        }

        $parsed = $this->parseReference($reference);

        if (! $parsed) {
            return null;
        }

        $verses = $this->fetchVerses($parsed['book'], $parsed['chapter'], $parsed['verse_numbers'], $versionAbbrev);

        if ($verses->isEmpty()) {
            return null;
        }

        return $verses->pluck('text')->implode(' ');
    }

    public function resolvePsalmResponse(string $reference, ?string $refrain = null, string $versionAbbrev = 'RSVCE'): ?string
    {
        $passage = $this->resolve($reference, $versionAbbrev);

        if (! $passage) {
            return $refrain;
        }

        if ($refrain && str_starts_with(trim($refrain), 'R.')) {
            return trim($refrain)."\n\n".$passage;
        }

        return 'R. (see response in psalm text below)'."\n\n".$passage;
    }

    /**
     * @return array{book: string, chapter: int, verse_numbers: array<int, int>}|null
     */
    protected function parseReference(string $reference): ?array
    {
        if (! preg_match('/^(.+?)\s+(\d+)\s*:\s*(.+)$/u', $reference, $matches)) {
            return null;
        }

        $book = trim($matches[1]);
        $chapter = (int) $matches[2];
        $versePart = trim($matches[3]);
        $verseNumbers = $this->parseVerseNumbers($versePart);

        if ($verseNumbers === []) {
            return null;
        }

        return [
            'book' => $this->bookAliases[$book] ?? $book,
            'chapter' => $chapter,
            'verse_numbers' => $verseNumbers,
        ];
    }

    protected function resolvePsalmPassage(string $reference, string $versionAbbrev): ?string
    {
        if (! preg_match('/^psalm\s+(\d+)\s*:\s*(.+)$/iu', $reference, $matches)) {
            if (preg_match('/^psalm\s+(\d+)$/iu', $reference, $simple)) {
                return $this->fetchVerses('Psalms', (int) $simple[1], range(1, 999), $versionAbbrev)
                    ->pluck('text')
                    ->implode(' ') ?: null;
            }

            return null;
        }

        $chapter = (int) $matches[1];
        $verseNumbers = $this->parseVerseNumbers(trim($matches[2]));
        $verses = $this->fetchVerses('Psalms', $chapter, $verseNumbers, $versionAbbrev);

        return $verses->isEmpty() ? null : $verses->pluck('text')->implode(' ');
    }

    /**
     * @return array<int, int>
     */
    protected function parseVerseNumbers(string $versePart): array
    {
        $numbers = [];

        foreach (preg_split('/\s*,\s*/', $versePart) ?: [] as $segment) {
            $segment = trim($segment);

            if ($segment === '') {
                continue;
            }

            if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $segment, $range)) {
                foreach (range((int) $range[1], (int) $range[2]) as $number) {
                    $numbers[] = $number;
                }

                continue;
            }

            if (preg_match('/^\d+$/', $segment)) {
                $numbers[] = (int) $segment;
            }
        }

        return array_values(array_unique($numbers));
    }

    /**
     * @param  array<int, int>  $verseNumbers
     */
    protected function fetchVerses(string $bookName, int $chapterNumber, array $verseNumbers, string $versionAbbrev): Collection
    {
        $version = BibleVersion::query()->where('abbreviation', $versionAbbrev)->first();

        if (! $version) {
            return collect();
        }

        $book = $this->findBook($version->id, $bookName);

        if (! $book) {
            return collect();
        }

        return BibleVerse::query()
            ->whereHas('chapter', function ($query) use ($book, $chapterNumber) {
                $query->where('bible_book_id', $book->id)
                    ->where('chapter_number', $chapterNumber);
            })
            ->when($verseNumbers !== [] && $verseNumbers !== range(1, 999), function ($query) use ($verseNumbers) {
                $query->whereIn('verse_number', $verseNumbers);
            })
            ->orderBy('verse_number')
            ->get();
    }

    protected function findBook(int $versionId, string $bookName): ?BibleBook
    {
        $names = $this->bookNamesForVersion($versionId);
        $normalizedTarget = $this->normalizeBookName($bookName);

        foreach ($names as $name => $book) {
            if ($this->normalizeBookName($name) === $normalizedTarget) {
                return $book;
            }
        }

        return null;
    }

    /**
     * @return array<string, BibleBook>
     */
    protected function bookNamesForVersion(int $versionId): array
    {
        if ($this->booksByVersion === null) {
            $this->booksByVersion = [];
        }

        if (isset($this->booksByVersion[$versionId])) {
            return $this->booksByVersion[$versionId];
        }

        $books = [];

        foreach (BibleBook::query()->where('bible_version_id', $versionId)->get() as $book) {
            $books[$book->name] = $book;
        }

        return $this->booksByVersion[$versionId] = $books;
    }

    protected function normalizeBookName(string $name): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($name)) ?? trim($name));
    }

    protected function buildPsalmUrl(string $reference, string $versionAbbrev): ?string
    {
        if (preg_match('/^psalm\s+(\d+)\s*:\s*(.+)$/iu', $reference, $matches)) {
            $chapter = (int) $matches[1];
            $firstVerse = $this->firstVerseFromPart(trim($matches[2]));
        } elseif (preg_match('/^psalm\s+(\d+)$/iu', $reference, $simple)) {
            $chapter = (int) $simple[1];
            $firstVerse = 1;
        } else {
            return null;
        }

        return $this->buildUrlFromParts('Psalms', $chapter, $firstVerse, $versionAbbrev);
    }

    protected function buildUrlFromParts(string $bookName, int $chapter, int $verse, string $versionAbbrev): ?string
    {
        $canonicalBook = $this->bookAliases[$bookName] ?? $bookName;
        $bookSlug = $this->bookAbbreviations[$canonicalBook] ?? null;

        if (! $bookSlug) {
            foreach ($this->versionCandidates($versionAbbrev) as $abbreviation) {
                $version = BibleVersion::query()->where('abbreviation', $abbreviation)->first();

                if (! $version) {
                    continue;
                }

                $book = $this->findBook($version->id, $bookName);

                if (! $book) {
                    continue;
                }

                $bookSlug = $book->abbreviation ?: (string) $book->id;
                $versionAbbrev = $abbreviation;

                break;
            }
        }

        if (! $bookSlug) {
            return null;
        }

        return "/bible/{$versionAbbrev}/{$bookSlug}/{$chapter}#v{$verse}";
    }

    /**
     * @return array<int, string>
     */
    protected function versionCandidates(string $preferred): array
    {
        return array_values(array_unique([$preferred, 'NABRE', 'RSVCE', 'DR']));
    }

    protected function firstVerseFromPart(string $versePart): int
    {
        if (preg_match('/^(\d+)/', $versePart, $matches)) {
            return (int) $matches[1];
        }

        return 1;
    }
}
