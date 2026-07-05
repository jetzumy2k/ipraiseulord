<?php

namespace Database\Seeders;

use App\Services\MassGuideFetchService;
use Illuminate\Database\Seeder;

class MassGuideSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(MassGuideFetchService::class);
        $currentYear = (int) now()->year;

        foreach ([$currentYear, $currentYear + 1] as $year) {
            $service->refreshYear($year);
        }
    }
}
