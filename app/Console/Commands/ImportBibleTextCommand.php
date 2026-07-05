<?php

namespace App\Console\Commands;

use App\Models\BibleVersion;
use App\Services\BibleTextImportService;
use Illuminate\Console\Command;

class ImportBibleTextCommand extends Command
{
    protected $signature = 'bible:import-text {version? : Bible version abbreviation (e.g. KJV, ADB, CEB, RVR)}';

    protected $description = 'Import complete Bible verse text for all or one version';

    public function handle(BibleTextImportService $service): int
    {
        $abbreviation = $this->argument('version');

        if ($abbreviation) {
            $version = BibleVersion::query()
                ->where('abbreviation', strtoupper($abbreviation))
                ->first();

            if (! $version) {
                $this->error("Bible version [{$abbreviation}] was not found.");

                return self::FAILURE;
            }

            $this->info("Importing {$version->abbreviation}...");
            $count = $service->importVersion($version);
            $this->info("Imported {$count} verses for {$version->abbreviation}.");

            return self::SUCCESS;
        }

        $this->info('Downloading Bible sources if needed and importing all versions...');

        $results = $service->importAll();

        foreach ($results as $abbrev => $count) {
            $this->line("  {$abbrev}: {$count} verses");
        }

        $this->info('Bible text import completed.');

        return self::SUCCESS;
    }
}
