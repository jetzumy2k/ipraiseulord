<?php

namespace Database\Seeders;

use App\Models\BibleVersion;
use App\Services\BibleStructureService;
use App\Services\BibleTextImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BibleSeeder extends Seeder
{
    public function run(): void
    {
        $dataPath = database_path('seeders/data/bible');
        $structure = app(BibleStructureService::class);

        $versions = json_decode(File::get("{$dataPath}/versions.json"), true);

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

            $structure->ensureVersionStructure($version);
        }

        app(BibleTextImportService::class)->importAll();
    }
}
