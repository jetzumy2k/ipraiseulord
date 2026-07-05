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
        return [
            $this->buildNovena(
                'Our Lady of Perpetual Help',
                'our-lady-of-perpetual-help',
                'Our Lady of Perpetual Help',
                'This novena honors Mary under the title of Our Lady of Perpetual Help, asking her maternal intercession for our needs and the needs of the whole Church.',
                'O Mother of Perpetual Help, grant that I may ever invoke thy most powerful name, which is the safeguard of the living and the salvation of the dying.',
                'O Mother of Perpetual Help, grant that I may ever invoke thy most powerful name. Obtain for me from thy beloved Son all the graces I need for my salvation. Amen.',
                [
                    'Confidence in Mary\'s Intercession',
                    'Trust in Divine Mercy',
                    'Healing of Body and Soul',
                    'Protection of Families',
                    'Guidance for the Church',
                    'Consolation for the Sorrowful',
                    'Strength in Temptation',
                    'Hope for the Dying',
                    'Perseverance in Faith',
                ]
            ),
            $this->buildNovena(
                'Sacred Heart of Jesus',
                'sacred-heart-of-jesus',
                'Sacred Heart of Jesus',
                'Nine days of devotion to the Sacred Heart of Jesus, honoring His infinite love and seeking reparation for sins against His most loving Heart.',
                'O most holy Heart of Jesus, fountain of every blessing, I adore You, I love You, and with a lively sorrow for my sins, I offer You this poor heart of mine.',
                'Sacred Heart of Jesus, I place all my trust in You. Immaculate Heart of Mary, pray for us. Saint Joseph, pray for us.',
                [
                    'The Love of the Sacred Heart',
                    'Reparation for Sins',
                    'Consecration to the Sacred Heart',
                    'The Eucharistic Heart of Jesus',
                    'Mercy and Forgiveness',
                    'Peace in Families',
                    'Conversion of Sinners',
                    'Comfort for the Afflicted',
                    'Final Consecration and Thanksgiving',
                ]
            ),
            $this->buildNovena(
                'Santo Niño',
                'santo-nino',
                'Santo Niño de Cebu',
                'A beloved Filipino devotion to the Holy Child Jesus, seeking His blessing upon families, communities, and the nation.',
                'Santo Niño, King of kings and Lord of lords, we come before You with childlike trust, asking for Your blessing upon our homes and our land.',
                'Santo Niño, bless our families, guide our leaders, and lead us ever closer to Your Sacred Heart. Viva Pit Señor!',
                [
                    'The Child Jesus as Our Model',
                    'Obedience and Humility',
                    'Blessing of Children',
                    'Healing and Wholeness',
                    'Protection from Harm',
                    'Prosperity of the Gospel',
                    'Unity of the Faithful',
                    'Gratitude for Blessings',
                    'Renewed Commitment to Christ',
                ]
            ),
            $this->buildNovena(
                'Saint Jude Thaddeus',
                'saint-jude',
                'Saint Jude Thaddeus',
                'Saint Jude, patron of hopeless cases, is invoked for desperate needs and seemingly impossible situations.',
                'Saint Jude, glorious apostle, faithful servant and friend of Jesus, the name of the traitor has caused you to be forgotten by many.',
                'Saint Jude, pray for us and all who honor thee and invoke thy aid. May we remain faithful to the teachings of Christ and His Church. Amen.',
                [
                    'Faith in Difficult Times',
                    'Patience in Suffering',
                    'Hope Against Despair',
                    'Courage in Trial',
                    'Trust in God\'s Plan',
                    'Intercession for the Sick',
                    'Reconciliation and Peace',
                    'Perseverance in Prayer',
                    'Gratitude for Answered Prayers',
                ]
            ),
            $this->buildNovena(
                'Immaculate Conception',
                'immaculate-conception',
                'Blessed Virgin Mary',
                'This novena prepares us to honor Mary\'s Immaculate Conception, celebrating her preservation from original sin from the first moment of her existence.',
                'O Mary, conceived without sin, pray for us who have recourse to thee. O Virgin Immaculate, we consecrate ourselves to thee.',
                'O Mary, conceived without sin, pray for us who have recourse to thee. Grant that we may always imitate thy purity and love. Amen.',
                [
                    'Mary\'s Privileged Grace',
                    'Purity of Heart',
                    'Maternal Protection',
                    'Victory Over Sin',
                    'Devotion to the Rosary',
                    'Intercession for the Church',
                    'Imitation of Mary\'s Virtues',
                    'Preparation for Her Feast',
                    'Consecration to the Immaculate Heart',
                ]
            ),
            $this->buildNovena(
                'Divine Mercy',
                'divine-mercy',
                'Jesus Christ, Divine Mercy',
                'The Chaplet of Divine Mercy novena, revealed to Saint Faustina, calls upon the infinite mercy of God for ourselves and the whole world.',
                'O Blood and Water, which gushed forth from the Heart of Jesus as a fountain of mercy for us, I trust in You.',
                'Eternal Father, I offer You the Body and Blood, Soul and Divinity of Your dearly beloved Son, Our Lord Jesus Christ, in atonement for our sins and those of the whole world. Jesus, I trust in You.',
                [
                    'Mercy for Sinners',
                    'Mercy for the Dying',
                    'Mercy for the Afflicted',
                    'Mercy for the Lost',
                    'Mercy for the Oppressed',
                    'Mercy for the Lonely',
                    'Mercy for the Faithful Departed',
                    'Mercy for the World',
                    'The Feast of Divine Mercy',
                ]
            ),
        ];
    }

    /**
     * @param  array<int, string>  $dayThemes
     * @return array<string, mixed>
     */
    protected function buildNovena(
        string $title,
        string $slug,
        string $patronSaint,
        string $description,
        string $openingPrayer,
        string $closingPrayer,
        array $dayThemes,
    ): array {
        $days = [];

        foreach ($dayThemes as $index => $theme) {
            $dayNumber = $index + 1;
            $days[] = [
                'day_number' => $dayNumber,
                'title' => "Day {$dayNumber}: {$theme}",
                'content' => "On this {$this->ordinal($dayNumber)} day of the {$title} novena, we meditate on {$theme}. "
                    ."Through the intercession of {$patronSaint}, we ask the Lord to deepen our faith, strengthen our hope, and enkindle our charity. "
                    .'Let us offer this day\'s intentions for our families, the Church, and all souls in need of God\'s grace.',
                'prayer' => "Lord Jesus Christ, through the intercession of {$patronSaint}, grant us the grace we need on this day of our novena. "
                    ."Help us to grow in {$theme}. We ask this in Your most holy name. Amen.",
            ];
        }

        return [
            'title' => $title,
            'slug' => $slug,
            'category' => 'common',
            'patron_saint' => $patronSaint,
            'description' => $description,
            'opening_prayer' => $openingPrayer,
            'closing_prayer' => $closingPrayer,
            'days' => $days,
        ];
    }

    protected function ordinal(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        $remainder = $number % 100;

        return $number.($suffixes[($remainder - 20) % 10] ?? $suffixes[$remainder] ?? $suffixes[0]);
    }
}
