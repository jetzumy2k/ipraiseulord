<?php

namespace App\Services;

use App\Models\Fiesta;
use Illuminate\Support\Facades\File;

class FiestaSeedService
{
    public function seed(): int
    {
        $path = database_path('seeders/data/fiestas.json');
        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            throw new \RuntimeException('Invalid fiesta seed data.');
        }

        $count = 0;

        foreach ($payload as $entry) {
            $attributes = [
                'title' => $entry['title'],
                'category' => $entry['category'],
                'honoree_name' => $entry['honoree_name'] ?? null,
                'description' => $entry['description'] ?? null,
                'liturgical_rank' => $entry['liturgical_rank'] ?? null,
                'is_movable' => (bool) ($entry['is_movable'] ?? false),
                'movable_rule' => $entry['movable_rule'] ?? null,
                'month' => $entry['month'] ?? null,
                'day' => $entry['day'] ?? null,
                'is_active' => (bool) ($entry['is_active'] ?? true),
                'sort_order' => (int) ($entry['sort_order'] ?? 0),
            ];

            if ($attributes['is_movable']) {
                Fiesta::withTrashed()->updateOrCreate(
                    [
                        'title' => $attributes['title'],
                        'movable_rule' => $attributes['movable_rule'],
                    ],
                    [...$attributes, 'deleted_at' => null]
                );
            } else {
                Fiesta::withTrashed()->updateOrCreate(
                    [
                        'title' => $attributes['title'],
                        'month' => $attributes['month'],
                        'day' => $attributes['day'],
                    ],
                    [...$attributes, 'deleted_at' => null]
                );
            }

            $count++;
        }

        return $count;
    }
}
