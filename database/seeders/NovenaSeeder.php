<?php

namespace Database\Seeders;

use App\Models\Novena;
use Illuminate\Database\Seeder;

class NovenaSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->novenas() as $novenaData) {
            $days = $novenaData['days'];
            unset($novenaData['days']);

            $novena = Novena::updateOrCreate(
                ['slug' => $novenaData['slug']],
                array_merge($novenaData, [
                    'duration_days' => 9,
                    'is_active' => true,
                ])
            );

            foreach ($days as $day) {
                $novena->days()->updateOrCreate(
                    ['day_number' => $day['day_number']],
                    [
                        'title' => $day['title'],
                        'content' => $day['content'],
                        'prayer' => $day['prayer'] ?? null,
                    ]
                );
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function novenas(): array
    {
        $novenas = [];
        $directory = database_path('seeders/data/novenas');

        require_once $directory.'/_helpers.php';

        foreach (glob($directory.'/*.php') as $file) {
            if (str_ends_with($file, '_helpers.php')) {
                continue;
            }

            $data = require $file;

            if (is_array($data) && isset($data['slug'], $data['days'])) {
                $novenas[] = $data;
            }
        }

        usort($novenas, fn (array $a, array $b) => strcmp($a['title'], $b['title']));

        return $novenas;
    }
}
