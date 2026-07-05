<?php

namespace App\Console\Commands;

use App\Services\FiestaSeedService;
use Illuminate\Console\Command;

class SeedFiestasCommand extends Command
{
    protected $signature = 'fiestas:seed';

    protected $description = 'Seed or refresh the Catholic fiesta calendar from the bundled liturgical data';

    public function handle(FiestaSeedService $service): int
    {
        $this->info('Seeding fiesta calendar...');

        $count = $service->seed();

        $this->info("Successfully seeded {$count} fiesta entries.");

        return self::SUCCESS;
    }
}
