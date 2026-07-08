<?php

namespace App\Services;

use App\Models\BibleBook;
use App\Models\BibleChapter;
use App\Models\BibleVerse;
use App\Models\BibleVersion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use SimpleXMLElement;

class BibleTextImportService
{
    /** @var array<string, string> */
    protected array $douayBookAliases = [
        'Josue' => 'Joshua',
        '1 Kings' => '1 Samuel',
        '2 Kings' => '2 Samuel',
        '3 Kings' => '1 Kings',
        '4 Kings' => '2 Kings',
        '1 Paralipomenon' => '1 Chronicles',
        '2 Paralipomenon' => '2 Chronicles',
        '1 Esdras' => 'Ezra',
        '2 Esdras' => 'Nehemiah',
        'Tobias' => 'Tobit',
        'Canticles' => 'Song of Songs',
        'Ecclesiasticus' => 'Sirach',
        'Isaias' => 'Isaiah',
        'Jeremias' => 'Jeremiah',
        'Ezechiel' => 'Ezekiel',
        'Osee' => 'Hosea',
        'Abdias' => 'Obadiah',
        'Jonas' => 'Jonah',
        'Micheas' => 'Micah',
        'Habacuc' => 'Habakkuk',
        'Sophonias' => 'Zephaniah',
        'Aggeus' => 'Haggai',
        'Zacharias' => 'Zechariah',
        'Malachias' => 'Malachi',
        '1 Machabees' => '1 Maccabees',
        '2 Machabees' => '2 Maccabees',
        'Apocalypse' => 'Revelation',
    ];

    /** @var array<string, string> */
    protected array $thiagobodrukBookAliases = [
        'Song of Solomon' => 'Song of Solomon',
        'Canticle of Canticles' => 'Song of Solomon',
        'Song of Songs' => 'Song of Solomon',
    ];

    /** @var array<int, string> */
    protected array $kjvDeuterocanonicalSourceBooks = [
        'Tobias',
        'Judith',
        'Wisdom',
        'Ecclesiasticus',
        'Baruch',
        '1 Machabees',
        '2 Machabees',
    ];

    /** @var array<string, string> */
    protected array $cebuanoBookAliases = [
        'Exodo' => 'Exodus',
        'Levitico' => 'Leviticus',
        'Numeros' => 'Numbers',
        'Deuteronomio' => 'Deuteronomy',
        'Josue' => 'Joshua',
        'Mga Maghuhukom' => 'Judges',
        'I Samuel' => '1 Samuel',
        'II Samuel' => '2 Samuel',
        'I Mga Hari' => '1 Kings',
        'II Mga Hari' => '2 Kings',
        'I Mga Cronicas' => '1 Chronicles',
        'II Mga Cronicas' => '2 Chronicles',
        'Esdras' => 'Ezra',
        'Nehemias' => 'Nehemiah',
        'Ester' => 'Esther',
        'Mga Salmo' => 'Psalms',
        'Mga Proverbio' => 'Proverbs',
        'Awit Ni Salomon' => 'Song of Solomon',
        'Isaias' => 'Isaiah',
        'Jeremias' => 'Jeremiah',
        'Lamentaciones' => 'Lamentations',
        'Ezequiel' => 'Ezekiel',
        'Oseas' => 'Hosea',
        'Abdias' => 'Obadiah',
        'Jonas' => 'Jonah',
        'Miqueas' => 'Micah',
        'Habacuc' => 'Habakkuk',
        'Sofonias' => 'Zephaniah',
        'Haggeo' => 'Haggai',
        'Zacarias' => 'Zechariah',
        'Malaquias' => 'Malachi',
        'Mateo' => 'Matthew',
        'Marcos' => 'Mark',
        'Lucas' => 'Luke',
        'Juan' => 'John',
        'Mga Buhat' => 'Acts',
        'Mga Taga-Roma' => 'Romans',
        'I Mga Taga-Corinto' => '1 Corinthians',
        'II Mga Taga-Corinto' => '2 Corinthians',
        'Mga Taga-Galacia' => 'Galatians',
        'Mga Taga-Efeso' => 'Ephesians',
        'Mga Taga-Filipos' => 'Philippians',
        'Mga Taga-Colosas' => 'Colossians',
        'I Mga Taga-Tesalonica' => '1 Thessalonians',
        'II Mga Taga-Tesalonica' => '2 Thessalonians',
        'I Kang Timoteo' => '1 Timothy',
        'II Kang Timoteo' => '2 Timothy',
        'Kang Tito' => 'Titus',
        'Kang Filemon' => 'Philemon',
        'Sa Mga Hebreohanon' => 'Hebrews',
        'Jacobo' => 'James',
        'I Pedro' => '1 Peter',
        'II Pedro' => '2 Peter',
        'I Juan' => '1 John',
        'II Juan' => '2 John',
        'III Juan' => '3 John',
        'Pinadayag' => 'Revelation',
    ];

    /** @var array<string, string> */
    protected array $adbBookAliases = [
        'Exodo' => 'Exodus',
        'Levitico' => 'Leviticus',
        'Bilang' => 'Numbers',
        'Deuteronomio' => 'Deuteronomy',
        'Josue' => 'Joshua',
        'Hukom' => 'Judges',
        '1 Hari' => '1 Kings',
        '2 Hari' => '2 Kings',
        '1 Cronica' => '1 Chronicles',
        '2 Cronica' => '2 Chronicles',
        'Nehemias' => 'Nehemiah',
        'Awit' => 'Psalms',
        'Kawikaan' => 'Proverbs',
        'Awit ni Solomon' => 'Song of Solomon',
        'Isaias' => 'Isaiah',
        'Jeremias' => 'Jeremiah',
        'Panaghoy' => 'Lamentations',
        'Oseas' => 'Hosea',
        'Obadias' => 'Obadiah',
        'Jonas' => 'Jonah',
        'Mikas' => 'Micah',
        'Habakuk' => 'Habakkuk',
        'Zefanias' => 'Zephaniah',
        'Hagai' => 'Haggai',
        'Zacarias' => 'Zechariah',
        'Malakias' => 'Malachi',
        'Mateo' => 'Matthew',
        'Marcos' => 'Mark',
        'Lucas' => 'Luke',
        'Juan' => 'John',
        'Gawa' => 'Acts',
        'Roma' => 'Romans',
        '1 Corinto' => '1 Corinthians',
        '2 Corinto' => '2 Corinthians',
        'Galacia' => 'Galatians',
        'Efeso' => 'Ephesians',
        'Filipos' => 'Philippians',
        'Colosas' => 'Colossians',
        '1 Tesalonica' => '1 Thessalonians',
        '2 Tesalonica' => '2 Thessalonians',
        '1 Timoteo' => '1 Timothy',
        '2 Timoteo' => '2 Timothy',
        '3 Timoteo' => '2 Timothy',
        'Tito' => 'Titus',
        'Filemon' => 'Philemon',
        'Hebreo' => 'Hebrews',
        'Santiago' => 'James',
        '1 Pedro' => '1 Peter',
        '2 Pedro' => '2 Peter',
        '1 Juan' => '1 John',
        '2 Juan' => '2 John',
        '3 Juan' => '3 John',
        'Apocalipsis' => 'Revelation',
    ];

    /** @var array<string, string> */
    protected array $odrBookSlugs = [
        'Genesis' => 'genesis',
        'Exodus' => 'exodus',
        'Leviticus' => 'leviticus',
        'Numbers' => 'numbers',
        'Deuteronomy' => 'deuteronomy',
        'Joshua' => 'josue',
        'Judges' => 'judges',
        'Ruth' => 'ruth',
        '1 Samuel' => '1-kings',
        '2 Samuel' => '2-kings',
        '1 Kings' => '3-kings',
        '2 Kings' => '4-kings',
        '1 Chronicles' => '1-paralipomenon',
        '2 Chronicles' => '2-paralipomenon',
        'Ezra' => '1-esdras',
        'Nehemiah' => '2-esdras',
        'Tobit' => 'tobias',
        'Judith' => 'judith',
        'Esther' => 'esther',
        '1 Maccabees' => '1-machabees',
        '2 Maccabees' => '2-machabees',
        'Job' => 'job',
        'Psalms' => 'psalms',
        'Proverbs' => 'proverbs',
        'Ecclesiastes' => 'ecclesiastes',
        'Song of Songs' => 'canticle-of-canticles',
        'Wisdom' => 'wisdom',
        'Sirach' => 'ecclesiasticus',
        'Isaiah' => 'isaie',
        'Jeremiah' => 'jeremie',
        'Lamentations' => 'lamentations',
        'Baruch' => 'baruch',
        'Ezekiel' => 'ezechiel',
        'Daniel' => 'daniel',
        'Hosea' => 'osee',
        'Joel' => 'joel',
        'Amos' => 'amos',
        'Obadiah' => 'abdias',
        'Jonah' => 'jonas',
        'Micah' => 'micheas',
        'Nahum' => 'nahum',
        'Habakkuk' => 'habacuc',
        'Zephaniah' => 'sophonias',
        'Haggai' => 'aggeus',
        'Zechariah' => 'zacharias',
        'Malachi' => 'malachie',
        'Matthew' => 'matthew',
        'Mark' => 'mark',
        'Luke' => 'luke',
        'John' => 'john',
        'Acts' => 'acts',
        'Romans' => 'romans',
        '1 Corinthians' => '1-corinthians',
        '2 Corinthians' => '2-corinthians',
        'Galatians' => 'galatians',
        'Ephesians' => 'ephesians',
        'Philippians' => 'philippians',
        'Colossians' => 'colossians',
        '1 Thessalonians' => '1-thessalonians',
        '2 Thessalonians' => '2-thessalonians',
        '1 Timothy' => '1-timothy',
        '2 Timothy' => '2-timothy',
        'Titus' => 'titus',
        'Philemon' => 'philemon',
        'Hebrews' => 'hebrews',
        'James' => 'james',
        '1 Peter' => '1-peter',
        '2 Peter' => '2-peter',
        '1 John' => '1-john',
        '2 John' => '2-john',
        '3 John' => '3-john',
        'Jude' => 'jude',
        'Revelation' => 'apocalypse',
    ];

    /** @var array<string, string> */
    protected array $sourceUrls = [
        'douay.json' => 'https://raw.githubusercontent.com/xxruyle/Bible-DouayRheims/main/Douay-Rheims/EntireBible-DR.json',
        'adb.csv' => 'https://raw.githubusercontent.com/rald/ADB/master/adb.csv',
        'ceb_p.xml' => 'https://ccel.org/ccel/b/bible/ceb_p.xml',
    ];

    protected string $cachePath;

    protected ?Collection $versionConfigs = null;

    public function __construct(protected BibleStructureService $structure)
    {
        $this->cachePath = storage_path('app/bible-cache');
    }

    /**
     * @return array<string, int>
     */
    public function importAll(): array
    {
        $this->structure->ensureAllBookStructures();

        $results = [];
        $versions = BibleVersion::query()->orderBy('id')->get();
        $kjvVersion = $versions->firstWhere('abbreviation', 'KJV');

        if ($kjvVersion) {
            $results[$kjvVersion->abbreviation] = $this->importVersion($kjvVersion, finalize: false);
        }

        foreach ($versions as $version) {
            if ($version->abbreviation === 'KJV') {
                continue;
            }

            $results[$version->abbreviation] = $this->importVersion($version, finalize: false);
        }

        $this->applyConfiguredSupplements($kjvVersion);
        $this->supplementMissingFromOdr();
        $this->structure->ensureAllBookStructures();
        $this->structure->syncBookChapterCountsFromStructure();
        $this->structure->refreshChapterVerseCounts();

        foreach (array_keys($results) as $abbreviation) {
            $version = BibleVersion::query()->where('abbreviation', $abbreviation)->firstOrFail();
            $results[$abbreviation] = $this->countVersesForVersion($version);
        }

        return $results;
    }

    public function importVersion(BibleVersion $version, bool $finalize = true): int
    {
        $this->structure->ensureVersionStructure($version);

        $config = $this->versionConfig($version->abbreviation);
        $import = $config['import'] ?? ['strategy' => 'douay'];

        $this->ensureSourceFile($import);

        match ($import['strategy']) {
            'douay' => $this->importDouay($version),
            'thiagobodruk' => $this->importThiagobodruk($version, $import['source']),
            'thiagobodruk_deuterocanonical' => $this->importThiagobodrukWithDeuterocanonical($version, $import['source']),
            'adb_csv' => $this->importAdbCsv($version),
            'ccel_thml' => $this->importCcelThml($version, $import['source']),
            default => throw new RuntimeException("Unknown import strategy [{$import['strategy']}] for {$version->abbreviation}."),
        };

        if ($finalize) {
            $supplementFrom = $import['supplement_from'] ?? null;

            if ($supplementFrom) {
                $sourceVersion = BibleVersion::query()->where('abbreviation', strtoupper($supplementFrom))->first();

                if ($sourceVersion) {
                    $this->supplementFromVersion($version, $sourceVersion);
                }
            }

            if (($config['canon'] ?? '') === 'catholic') {
                $this->supplementVersionFromOdr($version);
            }

            $this->structure->ensureVersionStructure($version);
            $this->structure->syncBookChapterCountsFromStructure();
            $this->structure->refreshChapterVerseCounts();
        }

        return $this->countVersesForVersion($version);
    }

    public function importDouay(BibleVersion $version): int
    {
        $booksByName = $this->booksByCanonicalName($version);
        $douayData = $this->loadJson($this->cachePath.'/douay.json');
        $imported = 0;

        DB::transaction(function () use ($douayData, $booksByName, &$imported) {
            foreach ($douayData as $sourceBook => $chapters) {
                if (! is_array($chapters)) {
                    continue;
                }

                $bookName = $this->normalizeBookName($sourceBook, $this->douayBookAliases);
                $book = $booksByName[$bookName] ?? null;

                if (! $book) {
                    continue;
                }

                $imported += $this->importDouayChapters($book, $chapters);
            }
        });

        return $imported;
    }

    public function importThiagobodruk(BibleVersion $version, string $sourceFile): int
    {
        $payload = $this->loadJson($this->cachePath.'/'.$sourceFile, true);
        $imported = 0;
        $useBookOrder = $version->books()->count() <= count($payload);

        DB::transaction(function () use ($payload, $version, $useBookOrder, &$imported) {
            $booksByOrder = $useBookOrder ? $this->booksByOrder($version) : [];
            $booksByName = $useBookOrder ? [] : $this->booksByCanonicalName($version);
            $aliases = $useBookOrder ? [] : $this->thiagobodrukAliasesFor($version);

            foreach ($payload as $bookIndex => $bookPayload) {
                if ($useBookOrder) {
                    $book = $booksByOrder[$bookIndex + 1] ?? null;
                } else {
                    $bookName = $this->resolveThiagobodrukBookName($bookPayload);
                    $bookName = $this->normalizeBookName($bookName, $aliases);
                    $book = $booksByName[$bookName] ?? null;
                }

                if (! $book || ! isset($bookPayload['chapters']) || ! is_array($bookPayload['chapters'])) {
                    continue;
                }

                foreach ($bookPayload['chapters'] as $chapterIndex => $verses) {
                    if (! is_array($verses)) {
                        continue;
                    }

                    $chapter = $this->chapterForBook($book, $chapterIndex + 1);
                    $imported += $this->importVerseList($chapter, $verses);
                }
            }
        });

        return $imported;
    }

    public function importThiagobodrukWithDeuterocanonical(BibleVersion $version, string $sourceFile): int
    {
        $imported = $this->importThiagobodruk($version, $sourceFile);

        $booksByName = $this->booksByCanonicalName($version);
        $douayData = $this->loadJson($this->cachePath.'/douay.json');

        DB::transaction(function () use ($douayData, $booksByName, &$imported) {
            foreach ($this->kjvDeuterocanonicalSourceBooks as $sourceBook) {
                $bookName = $this->normalizeBookName($sourceBook, $this->douayBookAliases);
                $book = $booksByName[$bookName] ?? null;
                $chapters = $douayData[$sourceBook] ?? null;

                if (! $book || ! is_array($chapters)) {
                    continue;
                }

                $imported += $this->importDouayChapters($book, $chapters);
            }
        });

        return $imported;
    }

    public function importAdbCsv(BibleVersion $version): int
    {
        $booksByName = $this->booksByCanonicalName($version);
        $path = $this->cachePath.'/adb.csv';
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Unable to open Tagalog Bible CSV at {$path}.");
        }

        $imported = 0;
        $batch = [];

        DB::transaction(function () use ($handle, $booksByName, &$imported, &$batch) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                $parts = explode('|', $line, 4);

                if (count($parts) < 4) {
                    continue;
                }

                [$sourceBook, $chapterNumber, $verseNumber, $text] = $parts;
                $bookName = $this->normalizeBookName($sourceBook, $this->adbBookAliases);
                $book = $booksByName[$bookName] ?? null;

                if (! $book) {
                    continue;
                }

                $chapter = $this->chapterForBook($book, (int) $chapterNumber);
                $batch[$chapter->id][(int) $verseNumber] = $this->cleanText($text);
            }

            foreach ($batch as $chapterId => $verseMap) {
                $chapter = BibleChapter::query()->find($chapterId);

                if ($chapter) {
                    $imported += $this->importVerseMap($chapter, $verseMap);
                }
            }
        });

        fclose($handle);

        return $imported;
    }

    public function importCcelThml(BibleVersion $version, string $sourceFile): int
    {
        $booksByName = $this->booksByCanonicalName($version);
        $path = $this->cachePath.'/'.$sourceFile;
        $xml = @simplexml_load_file($path, SimpleXMLElement::class, LIBXML_NOCDATA);

        if ($xml === false) {
            throw new RuntimeException("Invalid CCEL ThML Bible XML at {$path}.");
        }

        $imported = 0;

        DB::transaction(function () use ($xml, $booksByName, &$imported) {
            $bookNodes = $xml->xpath('//div2') ?: [];

            foreach ($bookNodes as $bookNode) {
                $sourceTitle = trim((string) ($bookNode['title'] ?? ''));
                $bookName = $this->normalizeBookName($sourceTitle, $this->cebuanoBookAliases);
                $book = $booksByName[$bookName] ?? null;

                if (! $book) {
                    continue;
                }

                $chapterNodes = $bookNode->xpath('./div3') ?: [];

                foreach ($chapterNodes as $chapterNode) {
                    $chapterTitle = trim((string) ($chapterNode['title'] ?? ''));

                    if (! preg_match('/(\d+)/', $chapterTitle, $matches)) {
                        continue;
                    }

                    $chapter = $this->chapterForBook($book, (int) $matches[1]);
                    $verseMap = $this->parseCcelChapterVerses($chapterNode);
                    $imported += $this->importVerseMap($chapter, $verseMap);
                }
            }
        });

        return $imported;
    }

    /**
     * @return array<int, string>
     */
    protected function parseCcelChapterVerses(SimpleXMLElement $chapterNode): array
    {
        $verseMap = [];
        $paragraphs = $chapterNode->xpath('.//p') ?: [];

        foreach ($paragraphs as $paragraph) {
            $innerXml = $paragraph->asXML() ?? '';

            if ($innerXml === '') {
                continue;
            }

            if (! preg_match_all('/<sup>(\d+)<\/sup>\s*(.*?)(?=<sup>\d+<\/sup>|$)/su', $innerXml, $matches, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($matches as $match) {
                $verseNumber = (int) $match[1];
                $text = strip_tags($match[2]);
                $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $text = $this->cleanText($text);

                if ($text !== '') {
                    $verseMap[$verseNumber] = $text;
                }
            }
        }

        return $verseMap;
    }

    /**
     * @param  array<string, mixed>  $bookPayload
     */
    protected function resolveThiagobodrukBookName(array $bookPayload): string
    {
        foreach (['name', 'book'] as $key) {
            if (! empty($bookPayload[$key])) {
                return (string) $bookPayload[$key];
            }
        }

        return (string) ($bookPayload['abbrev'] ?? '');
    }

    /**
     * @return array<string, string>
     */
    protected function thiagobodrukAliasesFor(BibleVersion $version): array
    {
        $aliases = $this->thiagobodrukBookAliases;
        $config = $this->versionConfigs()->get(strtoupper($version->abbreviation));

        if (($config['canon'] ?? '') === 'catholic') {
            $aliases['Song of Solomon'] = 'Song of Songs';
        }

        return $aliases;
    }

    /**
     * @param  array<string, array<string, mixed>>  $import
     */
    protected function ensureSourceFile(array $import): void
    {
        File::ensureDirectoryExists($this->cachePath);

        $strategy = $import['strategy'] ?? 'douay';

        $files = match ($strategy) {
            'douay' => ['douay.json'],
            'thiagobodruk' => [$import['source']],
            'thiagobodruk_deuterocanonical' => [$import['source'], 'douay.json'],
            'adb_csv' => ['adb.csv'],
            'ccel_thml' => [$import['source']],
            default => [],
        };

        foreach ($files as $file) {
            $path = $this->cachePath.'/'.$file;

            if (! File::exists($path)) {
                $this->downloadSource($file);
            }
        }
    }

    protected function downloadSource(string $filename): void
    {
        if (str_ends_with($filename, '.json') && ! isset($this->sourceUrls[$filename])) {
            $url = "https://raw.githubusercontent.com/thiagobodruk/bible/master/json/{$filename}";
        } else {
            $url = $this->sourceUrls[$filename] ?? null;
        }

        if (! $url) {
            throw new RuntimeException("Unknown Bible source file [{$filename}].");
        }

        $response = Http::timeout(180)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("Failed to download Bible source from {$url}.");
        }

        File::put($this->cachePath.'/'.$filename, $response->body());
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
        if ($this->versionConfigs === null) {
            $path = database_path('seeders/data/bible/versions.json');
            $payload = json_decode(File::get($path), true);

            if (! is_array($payload)) {
                throw new RuntimeException('Invalid Bible versions configuration.');
            }

            $this->versionConfigs = collect($payload)->keyBy(
                fn (array $config) => strtoupper($config['abbreviation'])
            );
        }

        return $this->versionConfigs;
    }

    /**
     * @param  array<string, array<string, string>>  $chapters
     */
    protected function importDouayChapters(BibleBook $book, array $chapters): int
    {
        $imported = 0;

        foreach ($chapters as $chapterNumber => $verses) {
            if (! is_array($verses)) {
                continue;
            }

            $chapter = $this->chapterForBook($book, (int) $chapterNumber);
            $verseMap = [];

            foreach ($verses as $verseNumber => $text) {
                $verseMap[(int) $verseNumber] = (string) $text;
            }

            $imported += $this->importVerseMap($chapter, $verseMap);
        }

        return $imported;
    }

    /**
     * @param  array<int, string>  $verses
     */
    protected function importVerseList(BibleChapter $chapter, array $verses): int
    {
        $verseMap = [];

        foreach ($verses as $index => $text) {
            $verseMap[$index + 1] = (string) $text;
        }

        return $this->importVerseMap($chapter, $verseMap);
    }

    /**
     * @param  array<int, string>  $verseMap
     */
    protected function importVerseMap(BibleChapter $chapter, array $verseMap): int
    {
        ksort($verseMap, SORT_NUMERIC);

        $rows = [];
        $now = now();

        foreach ($verseMap as $verseNumber => $text) {
            $rows[] = [
                'bible_chapter_id' => $chapter->id,
                'verse_number' => $verseNumber,
                'text' => $this->cleanText($text),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            $chapter->update(['verse_count' => 0]);

            return 0;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            BibleVerse::upsert(
                $chunk,
                ['bible_chapter_id', 'verse_number'],
                ['text', 'updated_at']
            );
        }

        $chapter->update(['verse_count' => count($rows)]);

        return count($rows);
    }

    protected function chapterForBook(BibleBook $book, int $chapterNumber): BibleChapter
    {
        $chapter = BibleChapter::withTrashed()->firstOrNew([
            'bible_book_id' => $book->id,
            'chapter_number' => $chapterNumber,
        ]);

        if ($chapter->trashed()) {
            $chapter->restore();
        }

        if (! $chapter->exists || $chapter->verse_count === null) {
            $chapter->verse_count = 0;
        }

        $chapter->save();

        return $chapter;
    }

    /**
     * @return array<int, BibleBook>
     */
    protected function booksByOrder(BibleVersion $version): array
    {
        $books = [];

        foreach ($version->books()->orderBy('book_order')->get() as $book) {
            $books[$book->book_order] = $book;
        }

        return $books;
    }

    /**
     * @return array<string, BibleBook>
     */
    protected function booksByCanonicalName(BibleVersion $version): array
    {
        $books = [];
        $localeMap = $this->localizedBookNameMap($version);

        foreach ($version->books()->get() as $book) {
            $canonical = array_search($book->name, $localeMap, true) ?: $book->name;
            $books[$canonical] = $book;
            $books[$book->name] = $book;
        }

        return $books;
    }

    /**
     * @return array<string, string>
     */
    protected function localizedBookNameMap(BibleVersion $version): array
    {
        $config = $this->versionConfigs()->get(strtoupper($version->abbreviation));
        $localeKey = $config['book_names'] ?? null;

        if (! $localeKey) {
            return [];
        }

        $path = database_path('seeders/data/bible/book_name_locales.json');
        $payload = json_decode(File::get($path), true);

        return is_array($payload[$localeKey] ?? null) ? $payload[$localeKey] : [];
    }

    protected function normalizeBookName(string $sourceName, array $aliases): string
    {
        return $aliases[$sourceName] ?? $sourceName;
    }

    protected function cleanText(string $text): string
    {
        $text = preg_replace('/\{[^}]*\}/', '', $text) ?? $text;
        $text = str_replace('*', '', $text);
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? trim($text);

        return $text;
    }

    /**
     * @return array<mixed>
     */
    protected function loadJson(string $path, bool $stripBom = false): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("Bible source file not found at {$path}.");
        }

        $contents = File::get($path);

        if ($stripBom) {
            $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents) ?? $contents;
        }

        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            throw new RuntimeException("Invalid Bible JSON at {$path}.");
        }

        return $payload;
    }

    protected function supplementFromVersion(BibleVersion $targetVersion, BibleVersion $sourceVersion): void
    {
        $sourceBooks = $this->booksByCanonicalName($sourceVersion);

        foreach ($this->booksByCanonicalName($targetVersion) as $bookName => $book) {
            $sourceBook = $sourceBooks[$bookName] ?? null;

            if (! $sourceBook || $book->id === $sourceBook->id) {
                continue;
            }

            $expectedChapters = (int) $book->chapter_count;

            for ($chapterNumber = 1; $chapterNumber <= $expectedChapters; $chapterNumber++) {
                $targetChapter = $this->chapterForBook($book, $chapterNumber);
                $sourceChapter = BibleChapter::query()
                    ->where('bible_book_id', $sourceBook->id)
                    ->where('chapter_number', $chapterNumber)
                    ->where('verse_count', '>', 0)
                    ->with('verses')
                    ->first();

                if (! $sourceChapter) {
                    continue;
                }

                $existingVerseNumbers = $targetChapter->verses()
                    ->pluck('verse_number')
                    ->all();
                $existingSet = array_flip($existingVerseNumbers);
                $verseMap = [];

                foreach ($sourceChapter->verses as $verse) {
                    if (! isset($existingSet[$verse->verse_number])) {
                        $verseMap[$verse->verse_number] = $verse->text;
                    }
                }

                if ($verseMap !== []) {
                    $this->importVerseMap($targetChapter, $verseMap);
                }
            }
        }
    }

    protected function applyConfiguredSupplements(?BibleVersion $kjvVersion): void
    {
        foreach ($this->versionConfigs() as $config) {
            $supplementFrom = $config['import']['supplement_from'] ?? null;

            if (! $supplementFrom) {
                continue;
            }

            $target = BibleVersion::query()->where('abbreviation', $config['abbreviation'])->first();
            $source = BibleVersion::query()->where('abbreviation', strtoupper($supplementFrom))->first();

            if (! $target || ! $source || $target->id === $source->id) {
                continue;
            }

            $this->supplementFromVersion($target, $source);
        }
    }

    protected function supplementMissingFromOdr(): void
    {
        foreach (BibleVersion::query()->orderBy('id')->get() as $version) {
            $config = $this->versionConfig($version->abbreviation);

            if (($config['canon'] ?? '') !== 'catholic') {
                continue;
            }

            $this->supplementVersionFromOdr($version);
        }
    }

    protected function supplementVersionFromOdr(BibleVersion $version): void
    {
        $localeMap = $this->localizedBookNameMap($version);

        foreach ($version->books()->orderBy('book_order')->get() as $book) {
            $canonicalName = array_search($book->name, $localeMap, true) ?: $book->name;
            $slug = $this->odrBookSlugs[$canonicalName] ?? null;

            if (! $slug || ! $this->bookNeedsOdrSupplement($book)) {
                continue;
            }

            $odrBook = $this->loadOdrBook($slug);

            foreach ($odrBook['chapters'] ?? [] as $chapterData) {
                if (! is_array($chapterData)) {
                    continue;
                }

                $chapterNumber = (int) ($chapterData['chapter'] ?? 0);

                if ($chapterNumber <= 0 || $chapterNumber > (int) $book->chapter_count) {
                    continue;
                }

                $targetChapter = $this->chapterForBook($book, $chapterNumber);
                $existingVerseNumbers = $targetChapter->verses()->pluck('verse_number')->all();
                $existingSet = array_flip($existingVerseNumbers);
                $verseMap = [];

                foreach ($chapterData['verses'] ?? [] as $verseData) {
                    if (! is_array($verseData)) {
                        continue;
                    }

                    $verseNumber = (int) ($verseData['verse'] ?? 0);
                    $text = $this->cleanText((string) ($verseData['text'] ?? ''));

                    if ($verseNumber <= 0 || $text === '' || isset($existingSet[$verseNumber])) {
                        continue;
                    }

                    $verseMap[$verseNumber] = $text;
                }

                if ($verseMap !== []) {
                    $this->importVerseMap($targetChapter, $verseMap);
                }
            }
        }
    }

    protected function bookNeedsOdrSupplement(BibleBook $book): bool
    {
        for ($chapterNumber = 1; $chapterNumber <= (int) $book->chapter_count; $chapterNumber++) {
            $chapter = $book->chapters()->where('chapter_number', $chapterNumber)->first();

            if (! $chapter || (int) $chapter->verse_count === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadOdrBook(string $slug): array
    {
        $cacheDir = $this->cachePath.'/odr';
        $path = $cacheDir.'/'.$slug.'.json';

        File::ensureDirectoryExists($cacheDir);

        if (! File::exists($path)) {
            $response = Http::timeout(180)->get("https://thedouayrheims.com/data/odr/{$slug}.json");

            if (! $response->successful()) {
                throw new RuntimeException("Failed to download ODR Bible book [{$slug}].");
            }

            File::put($path, $response->body());
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            throw new RuntimeException("Invalid ODR Bible JSON for [{$slug}].");
        }

        return $payload;
    }

    protected function finalizeAllBooks(): void
    {
        $this->structure->syncBookChapterCountsFromStructure();
        $this->structure->refreshChapterVerseCounts();
    }

    protected function countVersesForVersion(BibleVersion $version): int
    {
        return (int) BibleVerse::query()
            ->whereIn(
                'bible_chapter_id',
                BibleChapter::query()
                    ->whereIn('bible_book_id', $version->books()->pluck('id'))
                    ->pluck('id')
            )
            ->count();
    }
}
