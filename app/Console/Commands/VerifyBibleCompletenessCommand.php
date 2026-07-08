<?php

namespace App\Console\Commands;

use App\Models\BibleVersion;
use App\Services\BibleStructureService;
use Illuminate\Console\Command;

class VerifyBibleCompletenessCommand extends Command
{
    protected $signature = 'bible:verify {version? : Bible version abbreviation (defaults to all active versions)}';

    protected $description = 'Report missing Bible chapters and verses compared to the canonical structure';

    public function handle(BibleStructureService $structure): int
    {
        $abbreviation = $this->argument('version');
        $reference = BibleVersion::query()->where('abbreviation', 'KJV')->first();

        $versions = $abbreviation
            ? BibleVersion::query()->where('abbreviation', strtoupper($abbreviation))->get()
            : BibleVersion::query()->where('is_active', true)->orderBy('abbreviation')->get();

        if ($versions->isEmpty()) {
            $this->error('No matching Bible versions were found.');

            return self::FAILURE;
        }

        $hasIssues = false;

        foreach ($versions as $version) {
            $issues = $structure->auditVersion($version, $reference);
            $this->line("<info>{$version->abbreviation}</info>: ".count($issues).' issue(s)');

            if ($issues === []) {
                continue;
            }

            $hasIssues = true;

            foreach (array_slice($issues, 0, 25) as $issue) {
                $this->warn($this->formatIssue($issue));
            }

            if (count($issues) > 25) {
                $this->warn('  ... and '.(count($issues) - 25).' more');
            }
        }

        if ($hasIssues) {
            $this->newLine();
            $this->comment('Run php artisan bible:import-text to repair missing text from source files.');

            return self::FAILURE;
        }

        $this->info('All checked Bible versions are complete.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $issue
     */
    protected function formatIssue(array $issue): string
    {
        $book = $issue['book'];
        $chapter = (int) $issue['chapter'];

        return match ($issue['issue']) {
            'missing_chapters' => "  {$book}: expected {$issue['expected']} chapters, found {$issue['actual']}",
            'chapter_missing' => "  {$book} {$chapter}: chapter shell missing",
            'empty_chapter' => "  {$book} {$chapter}: no verses imported",
            default => "  {$book} {$chapter}: expected {$issue['expected']} verses, found {$issue['actual']}",
        };
    }
}
