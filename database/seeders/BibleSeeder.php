<?php

namespace Database\Seeders;

use App\Models\BibleBook;
use App\Models\BibleChapter;
use App\Models\BibleVersion;
use App\Services\BibleTextImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BibleSeeder extends Seeder
{
    public function run(): void
    {
        $dataPath = database_path('seeders/data/bible');

        $versions = json_decode(File::get("{$dataPath}/versions.json"), true);
        $localeNames = json_decode(File::get("{$dataPath}/book_name_locales.json"), true);

        foreach ($versions as $versionData) {
            $version = BibleVersion::updateOrCreate(
                ['abbreviation' => $versionData['abbreviation']],
                [
                    'name' => $versionData['name'],
                    'language' => $versionData['language'],
                    'description' => $versionData['description'],
                    'is_active' => true,
                ]
            );

            $structureFile = ($versionData['canon'] ?? 'catholic') === 'protestant'
                ? 'books_structure_protestant.json'
                : 'books_structure.json';

            $booksStructure = json_decode(File::get("{$dataPath}/{$structureFile}"), true);
            $localizedNames = is_array($localeNames[$versionData['book_names'] ?? ''] ?? null)
                ? $localeNames[$versionData['book_names']]
                : [];

            $this->seedBooksStructure($version, $booksStructure, $localizedNames);
        }

        app(BibleTextImportService::class)->importAll();
    }

    /**
     * @param  array<int, array<string, mixed>>  $booksStructure
     * @param  array<string, string>  $localizedNames
     */
    protected function seedBooksStructure(BibleVersion $version, array $booksStructure, array $localizedNames = []): void
    {
        foreach ($booksStructure as $bookData) {
            $displayName = $localizedNames[$bookData['name']] ?? $bookData['name'];

            $book = BibleBook::updateOrCreate(
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

                $chapter->fill(['verse_count' => 0])->save();
            }
        }
    }
}
