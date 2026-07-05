<?php

namespace App\Console\Commands;

use App\Services\MassGuideFetchService;
use Illuminate\Console\Command;

class FetchMassGuidesCommand extends Command
{
    protected $signature = 'mass-guides:fetch {year? : The liturgical year to fetch}';

    protected $description = 'Fetch or generate mass guides for a given year';

    public function handle(MassGuideFetchService $service): int
    {
        $year = $this->argument('year') ? (int) $this->argument('year') : (int) now()->year;

        $this->info("Fetching mass guides for {$year}...");

        $count = $service->refreshYear($year);

        $this->info("Successfully refreshed {$count} mass guide entries.");

        return self::SUCCESS;
    }
}
