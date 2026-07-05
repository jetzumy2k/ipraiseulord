<?php

namespace App\Services;

use App\Models\Novena;
use App\Models\NovenaDay;

class NovenaGuideService
{
    public function build(Novena $novena): array
    {
        $patron = $novena->patron_saint ?: $novena->title;
        $invocation = $this->invocationResponse($novena->slug, $patron);

        $sections = [
            $this->openingSection($novena, $patron, $invocation),
        ];

        foreach ($novena->days as $day) {
            $sections[] = $this->daySection($day, $novena, $patron, $invocation);
        }

        $sections[] = $this->closingSection($novena, $patron, $invocation);

        return ['sections' => $sections];
    }

    /**
     * @return array<string, mixed>
     */
    protected function openingSection(Novena $novena, string $patron, string $invocation): array
    {
        $steps = [
            $this->step('Sign of the Cross', [
                $this->part('leader', 'In the name of the Father, and of the Son, and of the Holy Spirit.'),
                $this->part('all', 'Amen.'),
            ]),
            $this->step('Gathering', [
                $this->part('leader', "Dear brothers and sisters, we gather for this novena to {$patron}. Let us pray with one heart, placing our intentions before God."),
                $this->part('all', 'Lord, hear our prayer.'),
            ]),
            $this->step('Invitatory', [
                $this->part('leader', 'V. O God, come to our aid.'),
                $this->part('all', 'R. O Lord, make haste to help us.'),
                $this->part('leader', 'Glory to the Father, and to the Son, and to the Holy Spirit, as it was in the beginning, is now, and ever shall be, world without end.'),
                $this->part('all', 'Amen.'),
            ]),
        ];

        if ($novena->opening_prayer) {
            $steps[] = $this->step('Opening Prayer', [
                $this->part('leader', $novena->opening_prayer),
                $this->part('all', 'Amen.'),
            ]);
        }

        $steps[] = $this->step('Patron Invocation', [
            $this->part('leader', "Let us invoke {$patron}:"),
            $this->part('all', $invocation),
        ]);

        return [
            'id' => 'opening',
            'title' => 'Opening',
            'steps' => $steps,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function daySection(NovenaDay $day, Novena $novena, string $patron, string $invocation): array
    {
        $theme = $this->dayTheme($day);
        $ordinal = $this->ordinal($day->day_number);

        $steps = [
            $this->step("Day {$day->day_number} — {$theme}", [
                $this->part('leader', "We now pray the {$ordinal} day of the {$novena->title} novena, meditating on {$theme}."),
                $this->part('all', 'Come, Holy Spirit, fill the hearts of your faithful and kindle in them the fire of your love.'),
            ]),
        ];

        if ($day->content) {
            $steps[] = $this->step('Meditation', [
                $this->part('leader', $day->content),
                $this->part('all', $invocation),
            ]);
        }

        if ($day->prayer) {
            $steps[] = $this->step('Day Prayer', [
                $this->part('leader', $day->prayer),
                $this->part('all', 'Amen.'),
            ]);
        }

        $steps[] = $this->step('Shared Prayers', [
            $this->part('leader', 'Let us pray together the Our Father, the Hail Mary, and the Glory Be for our intentions and for the whole Church.'),
            $this->part('all', $this->ourFather()),
            $this->part('all', $this->hailMary()),
            $this->part('all', $this->gloryBe()),
        ]);

        return [
            'id' => "day-{$day->day_number}",
            'title' => "Day {$day->day_number}",
            'subtitle' => $theme,
            'steps' => $steps,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function closingSection(Novena $novena, string $patron, string $invocation): array
    {
        $steps = [
            $this->step('Final Thanksgiving', [
                $this->part('leader', "We have completed our novena to {$patron}. Let us thank God for the grace of these days of prayer."),
                $this->part('all', 'Thanks be to God.'),
            ]),
        ];

        if ($novena->closing_prayer) {
            $steps[] = $this->step('Closing Prayer', [
                $this->part('leader', $novena->closing_prayer),
                $this->part('all', 'Amen.'),
            ]);
        }

        $steps[] = $this->step('Final Invocation', [
            $this->part('leader', "Let us repeat our invocation to {$patron}:"),
            $this->part('all', $invocation),
            $this->part('leader', 'May almighty God bless us, the Father, and the Son, and the Holy Spirit.'),
            $this->part('all', 'Amen.'),
        ]);

        $steps[] = $this->step('Dismissal', [
            $this->part('leader', 'Go in peace, glorifying the Lord by your life.'),
            $this->part('all', 'Thanks be to God.'),
            $this->part('leader', 'In the name of the Father, and of the Son, and of the Holy Spirit.'),
            $this->part('all', 'Amen.'),
        ]);

        return [
            'id' => 'closing',
            'title' => 'Closing',
            'steps' => $steps,
        ];
    }

    protected function dayTheme(NovenaDay $day): string
    {
        if ($day->title && str_contains($day->title, ':')) {
            return trim(explode(':', $day->title, 2)[1]);
        }

        return $day->title ?: "Day {$day->day_number}";
    }

    protected function invocationResponse(string $slug, string $patron): string
    {
        return match ($slug) {
            'our-lady-of-perpetual-help' => 'O Mother of Perpetual Help, pray for us.',
            'sacred-heart-of-jesus' => 'Sacred Heart of Jesus, I place all my trust in You.',
            'santo-nino' => 'Santo Niño, bless us and keep us in your love. Viva Pit Señor!',
            'saint-jude' => 'Saint Jude, apostle and martyr, pray for us.',
            'immaculate-conception' => 'O Mary, conceived without sin, pray for us who have recourse to thee.',
            'divine-mercy' => 'For the sake of His sorrowful Passion, have mercy on us and on the whole world.',
            'saint-joseph' => 'Saint Joseph, foster-father of Jesus, pray for us.',
            'saint-therese' => 'Saint Thérèse, the Little Flower, pray for us.',
            'saint-anthony' => 'Saint Anthony of Padua, pray for us.',
            'miraculous-medal' => 'O Mary, conceived without sin, pray for us who have recourse to thee.',
            'our-lady-of-lourdes' => 'Our Lady of Lourdes, pray for us.',
            'pentecost' => 'Come, Holy Spirit, fill the hearts of your faithful.',
            'saint-rita' => 'Saint Rita of Cascia, advocate of the impossible, pray for us.',
            default => "{$patron}, pray for us.",
        };
    }

    protected function ourFather(): string
    {
        return 'Our Father, who art in heaven, hallowed be thy name; thy kingdom come; thy will be done on earth as it is in heaven. Give us this day our daily bread; and forgive us our trespasses as we forgive those who trespass against us; and lead us not into temptation, but deliver us from evil. Amen.';
    }

    protected function hailMary(): string
    {
        return 'Hail Mary, full of grace, the Lord is with thee; blessed art thou among women, and blessed is the fruit of thy womb, Jesus. Holy Mary, Mother of God, pray for us sinners, now and at the hour of our death. Amen.';
    }

    protected function gloryBe(): string
    {
        return 'Glory be to the Father, and to the Son, and to the Holy Spirit. As it was in the beginning, is now, and ever shall be, world without end. Amen.';
    }

    protected function ordinal(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        $remainder = $number % 100;

        return $number.($suffixes[($remainder - 20) % 10] ?? $suffixes[$remainder] ?? $suffixes[0]);
    }

    /**
     * @param  array<int, array<string, string>>  $parts
     * @return array<string, mixed>
     */
    protected function step(string $title, array $parts): array
    {
        return [
            'title' => $title,
            'parts' => $parts,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function part(string $role, string $text): array
    {
        return [
            'role' => $role,
            'label' => $this->roleLabel($role),
            'text' => $text,
        ];
    }

    protected function roleLabel(string $role): string
    {
        return match ($role) {
            'leader' => 'Leader',
            'people' => 'People',
            'all' => 'All',
            'note' => 'Note',
            default => ucfirst($role),
        };
    }
}
