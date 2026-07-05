<?php

namespace App\Services;

use App\Models\MassGuide;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class MassGuideFetchService
{
    /** @var array<string, string> */
    protected array $seasonColors = [
        'Advent' => 'purple',
        'Christmas' => 'white',
        'Ordinary Time' => 'green',
        'Lent' => 'purple',
        'Easter' => 'white',
    ];

    public function fetchForYear(?int $year = null): int
    {
        $year = $year ?? (int) now()->year;
        $count = 0;

        $period = CarbonPeriod::create(
            Carbon::create($year, 1, 1),
            Carbon::create($year, 12, 31)
        );

        foreach ($period as $date) {
            $readings = $this->resolveReadings($date);
            $season = $this->resolveSeason($date, $readings['feast_name'] ?? null);
            $color = $this->seasonColors[$season] ?? 'green';

            MassGuide::updateOrCreate(
                ['liturgical_date' => $date->toDateString()],
                array_merge($readings, [
                    'liturgical_season' => $season,
                    'liturgical_color' => $color,
                    'liturgical_year' => $this->liturgicalYear($date),
                ])
            );

            $count++;
        }

        return $count;
    }

    public function ensureYear(int $year): int
    {
        $lectionary = app(LectionaryReadingsService::class);
        $sampleDate = "{$year}-07-05";
        $existing = MassGuide::query()
            ->whereDate('liturgical_date', $sampleDate)
            ->first();

        $expected = $lectionary->resolveForDate(Carbon::parse($sampleDate));
        $needsRefresh = ! $existing
            || ($expected && $existing->first_reading_reference !== ($expected['first_reading_reference'] ?? null));

        if ($needsRefresh || MassGuide::query()->whereYear('liturgical_date', $year)->count() < 360) {
            $this->fetchForYear($year);
            $this->applyKeyFeasts($year);
        }

        return MassGuide::query()->whereYear('liturgical_date', $year)->count();
    }

    public function refreshYear(int $year): int
    {
        $this->fetchForYear($year);
        $this->applyKeyFeasts($year);

        return MassGuide::query()->whereYear('liturgical_date', $year)->count();
    }

    public function applyKeyFeasts(int $year): void
    {
        foreach ($this->keyFeastReadings($year) as $date => $data) {
            MassGuide::updateOrCreate(
                ['liturgical_date' => $date],
                array_merge($this->enrichReadings($data), [
                    'liturgical_year' => (int) substr($date, 0, 4) + (substr($date, 5, 2) >= '12' ? 1 : 0),
                ])
            );
        }
    }

    /**
     * @return array<string, string|null>
     */
    protected function resolveReadings(Carbon $date): array
    {
        $lectionary = app(LectionaryReadingsService::class);
        $resolved = $lectionary->resolveForDate($date);

        if ($resolved) {
            if (empty($resolved['feast_name'])) {
                $resolved['feast_name'] = $this->defaultFeastName($date);
            }

            if ($this->resolveSeason($date, $resolved['feast_name']) === 'Lent') {
                $resolved['alleluia_reference'] = null;
                $resolved['alleluia_text'] = null;
            }

            return $resolved;
        }

        return $this->fallbackReadings($date);
    }

    /**
     * @param  array<string, string|null>  $readings
     * @return array<string, string|null>
     */
    public function enrichReadings(array $readings): array
    {
        $resolver = app(BiblePassageResolver::class);

        foreach (['first_reading', 'second_reading', 'gospel'] as $prefix) {
            $reference = $readings["{$prefix}_reference"] ?? null;

            if (! $reference || str_contains($reference, ';')) {
                continue;
            }

            $resolved = $resolver->resolve($reference);

            if (! $resolved) {
                continue;
            }

            if ($prefix === 'second_reading' && ! str_starts_with($resolved, 'Brothers')) {
                $resolved = 'Brothers and sisters: '.$resolved;
            }

            $readings["{$prefix}_text"] = $resolved;
        }

        $psalmReference = $readings['responsorial_psalm_reference'] ?? null;

        if ($psalmReference && ! str_contains($psalmReference, ';')) {
            $psalmText = $resolver->resolvePsalmResponse(
                $psalmReference,
                $readings['responsorial_psalm_text'] ?? null
            );

            if ($psalmText) {
                $readings['responsorial_psalm_text'] = $psalmText;
            }
        }

        return $readings;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function keyFeastReadings(int $year): array
    {
        return [
            "{$year}-01-01" => [
                'feast_name' => 'Solemnity of Mary, Mother of God',
                'liturgical_season' => 'Christmas',
                'liturgical_color' => 'white',
                'first_reading_reference' => 'Numbers 6:22-27',
                'first_reading_text' => 'The LORD said to Moses: "Speak to Aaron and his sons and tell them: This is how you shall bless the Israelites. Say to them: The LORD bless you and keep you! The LORD let his face shine upon you, and be gracious to you! The LORD look upon you kindly and give you peace! So shall they invoke my name upon the Israelites, and I will bless them."',
                'responsorial_psalm_reference' => 'Psalm 67:2-3, 5, 6, 8',
                'responsorial_psalm_text' => 'R. May God bless us in his mercy. May God have pity on us and bless us; may he let his face shine upon us. So may your way be known upon earth; among all nations, your salvation.',
                'gospel_reference' => 'Luke 2:16-21',
                'gospel_text' => 'The shepherds went in haste to Bethlehem and found Mary and Joseph, and the infant lying in the manger. When they saw this, they made known the message that had been told them about this child. All who heard it were amazed by what had been told them by the shepherds. And Mary kept all these things, reflecting on them in her heart. Then the shepherds returned, glorifying and praising God for all they had heard and seen, just as it had been told to them. When eight days were completed for his circumcision, he was named Jesus, the name given him by the angel before he was conceived in the womb.',
            ],
            "{$year}-12-25" => [
                'feast_name' => 'Nativity of the Lord (Christmas)',
                'liturgical_season' => 'Christmas',
                'liturgical_color' => 'white',
                'first_reading_reference' => 'Isaiah 9:1-6',
                'first_reading_text' => 'The people who walked in darkness have seen a great light; upon those who dwelt in the land of gloom a light has shone. You have brought them abundant joy and great rejoicing, as they rejoice before you as at the harvest, as people make merry when dividing spoils. For a child is born to us, a son is given us; upon his shoulder dominion rests. They name him Wonder-Counselor, God-Hero, Father-Forever, Prince of Peace.',
                'responsorial_psalm_reference' => 'Psalm 96:1-2, 3, 11-13',
                'responsorial_psalm_text' => 'R. Today is born our Savior, Christ the Lord. Sing to the LORD a new song; sing to the LORD, all you lands. Sing to the LORD; bless his name; announce his salvation, day after day.',
                'gospel_reference' => 'Luke 2:1-14',
                'gospel_text' => 'In those days a decree went out from Caesar Augustus that the whole world should be enrolled. So all went to be enrolled, each to his own town. And Joseph too went up from Galilee from the town of Nazareth to Judea, to the city of David that is called Bethlehem, because he was of the house and family of David, to be enrolled with Mary, his betrothed, who was with child. While they were there, the time came for her to have her child, and she gave birth to her firstborn son. She wrapped him in swaddling clothes and laid him in a manger, because there was no room for them in the inn.',
            ],
        ];
    }

    /**
     * @return array<string, string|null>
     */
    protected function fallbackReadings(Carbon $date): array
    {
        $isSunday = $date->isSunday();

        $readings = [
            'feast_name' => $this->defaultFeastName($date),
            'first_reading_reference' => 'Isaiah 55:10-11',
            'first_reading_text' => null,
            'second_reading_reference' => $isSunday ? 'Romans 8:18-23' : null,
            'second_reading_text' => null,
            'responsorial_psalm_reference' => 'Psalm 65:10-13',
            'responsorial_psalm_text' => 'R. The seed that falls on good ground will yield a fruitful harvest.',
            'alleluia_reference' => $isSunday ? 'Matthew 13:23' : null,
            'alleluia_text' => $isSunday
                ? 'R. Alleluia, alleluia. Blessed are they who have kept the word with a generous heart. R. Alleluia, alleluia.'
                : null,
            'gospel_reference' => $isSunday ? 'Matthew 13:1-23' : 'Matthew 13:18-23',
            'gospel_text' => null,
        ];

        return $this->enrichReadings($readings);
    }

    protected function resolveSeason(Carbon $date, ?string $feastName = null): string
    {
        $feast = strtolower($feastName ?? '');

        if (str_contains($feast, 'advent')) {
            return 'Advent';
        }

        if (str_contains($feast, 'lent') || str_contains($feast, 'ash wednesday') || str_contains($feast, 'passion')) {
            return 'Lent';
        }

        if (str_contains($feast, 'easter') || str_contains($feast, 'pentecost') || str_contains($feast, 'ascension')) {
            return 'Easter';
        }

        if (str_contains($feast, 'christmas') || str_contains($feast, 'nativity') || str_contains($feast, 'epiphany') || str_contains($feast, 'holy family')) {
            return 'Christmas';
        }

        $calculator = app(FiestaDateCalculator::class);
        $year = $date->year;
        $easter = $calculator->easterSunday($year);
        $ashWednesday = $easter->copy()->subDays(46);
        $pentecost = $easter->copy()->addDays(49);
        $adventStart = Carbon::create($year, 12, 25)->subWeeks(4);

        while (! $adventStart->isSunday()) {
            $adventStart->subDay();
        }

        if ($date->between($adventStart, Carbon::create($year, 12, 24))) {
            return 'Advent';
        }

        if ($date->month === 12 && $date->day >= 25 || ($date->month === 1 && $date->day <= 12)) {
            return 'Christmas';
        }

        if ($date->between($ashWednesday, $easter->copy()->subDay())) {
            return 'Lent';
        }

        if ($date->between($easter, $pentecost->copy()->addWeeks(7))) {
            return 'Easter';
        }

        return 'Ordinary Time';
    }

    protected function defaultFeastName(Carbon $date): string
    {
        if ($date->isSunday()) {
            return 'Sunday of '.$this->resolveSeason($date);
        }

        return 'Weekday of '.$this->resolveSeason($date);
    }

    protected function liturgicalYear(Carbon $date): int
    {
        return $date->month >= 12 ? $date->year + 1 : $date->year;
    }
}
