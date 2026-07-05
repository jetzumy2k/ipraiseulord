<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class LectionaryReadingsService
{
    /** @var array<string, array<int, array<string, mixed>>>|null */
    protected ?array $readingsByDate = null;

    public function hasReadingsFor(string $date): bool
    {
        return isset($this->loadReadings()[$date]);
    }

    /**
     * @return array<string, string|null>|null
     */
    public function resolveForDate(Carbon $date): ?array
    {
        $entries = $this->loadReadings()[$date->toDateString()] ?? null;

        if (! $entries) {
            return null;
        }

        $record = $entries[0];
        $readings = $record['readings'] ?? [];
        $normalizer = app(CitationNormalizer::class);

        $firstReading = $this->firstCitation($readings['first_reading'] ?? []);
        $secondReading = $this->firstCitation($readings['second_reading'] ?? []);
        $psalm = $this->firstCitation($readings['responsorial_psalm'] ?? []);
        $alleluia = $this->firstCitation($readings['alleluia'] ?? []);
        $gospel = $this->firstCitation($readings['gospel'] ?? []);

        $result = [
            'feast_name' => $record['feast'] ?? null,
            'first_reading_reference' => $normalizer->normalize($firstReading),
            'first_reading_text' => null,
            'second_reading_reference' => $secondReading !== '' ? $normalizer->normalize($secondReading) : null,
            'second_reading_text' => null,
            'responsorial_psalm_reference' => $normalizer->normalize($psalm),
            'responsorial_psalm_text' => $this->defaultPsalmRefrain($record['feast'] ?? ''),
            'alleluia_reference' => $alleluia !== '' ? $normalizer->normalize($alleluia) : null,
            'alleluia_text' => $alleluia !== '' ? $this->defaultAlleluia($alleluia) : null,
            'gospel_reference' => $normalizer->normalize($gospel),
            'gospel_text' => null,
        ];

        return app(MassGuideFetchService::class)->enrichReadings($result);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function firstCitation(array $items): string
    {
        foreach ($items as $item) {
            $citation = trim($item['citation'] ?? '');

            if ($citation !== '') {
                return $citation;
            }
        }

        return '';
    }

    protected function defaultPsalmRefrain(string $feast): string
    {
        if (stripos($feast, 'easter') !== false || stripos($feast, 'octave') !== false) {
            return 'R. Alleluia, alleluia.';
        }

        return 'R. (see response in the responsorial psalm text below)';
    }

    protected function defaultAlleluia(string $citation): string
    {
        $verse = app(CitationNormalizer::class)->normalize($citation);

        return "R. Alleluia, alleluia.\n{$verse}\nR. Alleluia, alleluia.";
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function loadReadings(): array
    {
        if ($this->readingsByDate !== null) {
            return $this->readingsByDate;
        }

        $path = database_path('seeders/data/mass_guides/lectionary/readings.json');

        if (! File::exists($path)) {
            return $this->readingsByDate = [];
        }

        $payload = json_decode(File::get($path), true);

        return $this->readingsByDate = is_array($payload) ? $payload : [];
    }
}
