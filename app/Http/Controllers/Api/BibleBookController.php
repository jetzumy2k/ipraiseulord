<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\BibleBook;
use App\Models\BibleChapter;
use App\Models\BibleVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BibleBookController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return BibleBook::class;
    }

    protected function defaultRelations(): array
    {
        return ['version'];
    }

    protected function searchableFields(): array
    {
        return ['name', 'abbreviation'];
    }

    protected function filterableFields(): array
    {
        return ['bible_version_id', 'testament'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'book_order', 'name', 'created_at', 'updated_at'];
    }

    protected function importUniqueKeys(): array
    {
        return ['bible_version_id', 'book_order'];
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json,txt'],
            'bible_version_id' => ['nullable', 'integer', 'exists:bible_versions,id'],
        ]);

        $contents = file_get_contents($request->file('file')->getRealPath());
        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid JSON file.'], 422);
        }

        $items = array_is_list($payload) ? $payload : ($payload['data'] ?? []);

        if (! is_array($items) || $items === []) {
            return response()->json(['message' => 'JSON must be a non-empty array of records.'], 422);
        }

        $items = array_values(array_filter($items, 'is_array'));

        if ($this->isStructureImport($items)) {
            return $this->importStructure($items, $request);
        }

        try {
            return $this->importRecords($items);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function isStructureImport(array $items): bool
    {
        $first = $items[0];

        return ! array_key_exists('bible_version_id', $first)
            && array_key_exists('book_order', $first)
            && array_key_exists('name', $first);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function importStructure(array $items, Request $request): JsonResponse
    {
        $versionIds = $request->filled('bible_version_id')
            ? [(int) $request->input('bible_version_id')]
            : BibleVersion::query()->where('is_active', true)->pluck('id')->all();

        if ($versionIds === []) {
            return response()->json([
                'message' => 'Select a Bible version filter before importing structure data, or create an active Bible version first.',
            ], 422);
        }

        $imported = 0;

        DB::transaction(function () use ($items, $versionIds, &$imported) {
            foreach ($versionIds as $versionId) {
                foreach ($items as $item) {
                    $data = Validator::make($item, [
                        'name' => ['required', 'string', 'max:255'],
                        'abbreviation' => ['nullable', 'string', 'max:50'],
                        'testament' => ['required', Rule::in(['old', 'new'])],
                        'book_order' => ['required', 'integer', 'min:1'],
                        'chapter_count' => ['required', 'integer', 'min:0'],
                    ])->validate();

                    $book = BibleBook::updateOrCreate(
                        [
                            'bible_version_id' => $versionId,
                            'book_order' => $data['book_order'],
                        ],
                        [
                            'name' => $data['name'],
                            'abbreviation' => $data['abbreviation'] ?? null,
                            'testament' => $data['testament'],
                            'chapter_count' => $data['chapter_count'],
                        ]
                    );

                    for ($chapterNumber = 1; $chapterNumber <= $data['chapter_count']; $chapterNumber++) {
                        BibleChapter::updateOrCreate(
                            [
                                'bible_book_id' => $book->id,
                                'chapter_number' => $chapterNumber,
                            ],
                            ['verse_count' => 0]
                        );
                    }

                    $imported++;
                }
            }
        });

        $versionCount = count($versionIds);

        return response()->json([
            'message' => $versionCount > 1
                ? "Import completed for {$versionCount} Bible versions."
                : 'Import completed.',
            'imported' => $imported,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function importRecords(array $items): JsonResponse
    {
        $imported = 0;

        DB::transaction(function () use ($items, &$imported) {
            foreach ($items as $item) {
                $item = $this->sanitizeImportItem($item);

                if (! array_key_exists('bible_version_id', $item)) {
                    throw new \InvalidArgumentException('Each record must include bible_version_id, or import a books structure file without version IDs.');
                }

                $data = Validator::make($item, [
                    'bible_version_id' => ['required', 'exists:bible_versions,id'],
                    'name' => ['required', 'string', 'max:255'],
                    'abbreviation' => ['nullable', 'string', 'max:50'],
                    'testament' => ['required', Rule::in(['old', 'new'])],
                    'book_order' => ['required', 'integer', 'min:1'],
                    'chapter_count' => ['required', 'integer', 'min:0'],
                ])->validate();

                $existing = BibleBook::withTrashed()
                    ->where('bible_version_id', $data['bible_version_id'])
                    ->where('book_order', $data['book_order'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }
                    $existing->update($data);
                } else {
                    BibleBook::create($data);
                }

                $imported++;
            }
        });

        return response()->json([
            'message' => 'Import completed.',
            'imported' => $imported,
        ]);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function sanitizeImportItem(array $item): array
    {
        unset(
            $item['id'],
            $item['version'],
            $item['chapters'],
            $item['created_at'],
            $item['updated_at'],
            $item['deleted_at'],
        );

        return $item;
    }

    protected function storeRules(): array
    {
        return [
            'bible_version_id' => ['required', 'exists:bible_versions,id'],
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['nullable', 'string', 'max:50'],
            'testament' => ['required', Rule::in(['old', 'new'])],
            'book_order' => ['required', 'integer', 'min:1'],
            'chapter_count' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'bible_version_id' => ['sometimes', 'exists:bible_versions,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'abbreviation' => ['nullable', 'string', 'max:50'],
            'testament' => ['sometimes', Rule::in(['old', 'new'])],
            'book_order' => ['sometimes', 'integer', 'min:1'],
            'chapter_count' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
