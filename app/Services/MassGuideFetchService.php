<?php

namespace App\Services;

use App\Models\MassGuide;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\File;

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

    /** @var array<string, array<int, array<string, string|null>>>|null */
    protected ?array $readingsPool = null;

    public function fetchForYear(?int $year = null): int
    {
        $year = $year ?? (int) now()->year;
        $count = 0;

        $period = CarbonPeriod::create(
            Carbon::create($year, 1, 1),
            Carbon::create($year, 12, 31)
        );

        foreach ($period as $date) {
            $season = $this->resolveSeason($date);
            $color = $this->seasonColors[$season] ?? 'green';
            $feast = $this->resolveFeastName($date);
            $readings = $this->resolveReadings($date, $season);

            MassGuide::updateOrCreate(
                ['liturgical_date' => $date->toDateString()],
                array_merge($readings, [
                    'liturgical_season' => $season,
                    'liturgical_color' => $color,
                    'feast_name' => $feast,
                    'liturgical_year' => $this->liturgicalYear($date),
                ])
            );

            $count++;
        }

        return $count;
    }

    public function ensureYear(int $year): int
    {
        $existing = MassGuide::query()
            ->whereYear('liturgical_date', $year)
            ->count();

        if ($existing < 360) {
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
                array_merge($data, [
                    'liturgical_year' => (int) substr($date, 0, 4) + (substr($date, 5, 2) >= '12' ? 1 : 0),
                ])
            );
        }
    }

    /**
     * @return array<string, string|null>
     */
    protected function resolveReadings(Carbon $date, string $season): array
    {
        $pool = $this->loadReadingsPool();
        $isSunday = $date->isSunday();
        $sets = $pool[$isSunday ? 'sunday' : 'weekday'] ?? [];

        if ($sets === []) {
            $readings = $this->fallbackReadings($date, $isSunday);
        } else {
            $index = ($date->weekOfYear + ($isSunday ? 0 : 17)) % count($sets);
            $readings = $sets[$index];

            if (! $isSunday) {
                $readings['second_reading_reference'] = null;
                $readings['second_reading_text'] = null;
                $readings['alleluia_reference'] = null;
                $readings['alleluia_text'] = null;
            }
        }

        if ($season === 'Lent') {
            $readings['alleluia_reference'] = null;
            $readings['alleluia_text'] = null;
        } elseif ($isSunday && empty($readings['alleluia_text'])) {
            $readings['alleluia_reference'] = 'John 6:63';
            $readings['alleluia_text'] = 'R. Alleluia, alleluia. The words I have spoken to you are Spirit and life, says the Lord. R. Alleluia, alleluia.';
        }

        return $this->enrichFromBible($readings);
    }

    /**
     * @param  array<string, string|null>  $readings
     * @return array<string, string|null>
     */
    protected function enrichFromBible(array $readings): array
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
            "{$year}-01-06" => [
                'feast_name' => 'Epiphany of the Lord',
                'liturgical_season' => 'Christmas',
                'liturgical_color' => 'white',
                'first_reading_reference' => 'Isaiah 60:1-6',
                'first_reading_text' => 'Rise up in splendor, Jerusalem! Your light has come, the glory of the Lord shines upon you. See, darkness covers the earth, and thick clouds cover the peoples; but upon you the LORD shines, and over you appears his glory. Nations shall walk by your light, and kings by your shining radiance.',
                'responsorial_psalm_reference' => 'Psalm 72:1-2, 7-8, 10-11, 12-13',
                'responsorial_psalm_text' => 'R. Lord, every nation on earth will adore you. O God, with your judgment endow the king, and with your justice, the king\'s son. Justice shall flower in his days, and profound peace, till the moon be no more.',
                'gospel_reference' => 'Matthew 2:1-12',
                'gospel_text' => 'When Jesus was born in Bethlehem of Judea, in the days of King Herod, behold, magi from the east arrived in Jerusalem, saying, "Where is the newborn king of the Jews? We saw his star at its rising and have come to do him homage." When King Herod heard this, he was greatly troubled, and all Jerusalem with him. After their audience with the king they set out. And behold, the star that they had seen at its rising preceded them, until it came and stopped over the place where the child was. They were overjoyed at seeing the star, and on entering the house they saw the child with Mary his mother. They prostrated themselves and did him homage. Then they opened their treasures and offered him gifts of gold, frankincense, and myrrh.',
            ],
            "{$year}-03-19" => [
                'feast_name' => 'Solemnity of Saint Joseph, Spouse of the Blessed Virgin Mary',
                'liturgical_season' => 'Lent',
                'liturgical_color' => 'white',
                'first_reading_reference' => '2 Samuel 7:4-5, 12-14, 16',
                'first_reading_text' => 'The LORD spoke to Nathan and said: "Go, tell my servant David: When your time comes and you rest with your ancestors, I will raise up your heir after you, sprung from your loins, and I will make his kingdom firm. I will be a father to him, and he shall be a son to me. Your house and your kingdom shall endure forever before me; your throne shall stand firm forever."',
                'responsorial_psalm_reference' => 'Psalm 89:2-3, 4-5, 27, 29',
                'responsorial_psalm_text' => 'R. The son of David will live forever. The promises of the LORD I will sing forever; through all generations my mouth shall proclaim your faithfulness. For you have said, "My kindness is established forever"; in heaven you have confirmed your faithfulness.',
                'gospel_reference' => 'Matthew 1:16, 18-21',
                'gospel_text' => 'Jacob was the father of Joseph, the husband of Mary. Of her was born Jesus who is called the Christ. Now this is how the birth of Jesus Christ came about. When his mother Mary was betrothed to Joseph, but before they lived together, she was found with child through the Holy Spirit. Joseph her husband, since he was a righteous man, yet unwilling to expose her to shame, decided to divorce her quietly. Such was his intention when, behold, the angel of the Lord appeared to him in a dream and said, "Joseph, son of David, do not be afraid to take Mary your wife into your home. For it is through the Holy Spirit that this child has been conceived in her. She will bear a son and you are to name him Jesus, because he will save his people from their sins." When Joseph awoke, he did as the angel of the Lord had commanded him and took his wife into his home.',
            ],
            "{$year}-08-15" => [
                'feast_name' => 'Assumption of the Blessed Virgin Mary',
                'liturgical_season' => 'Ordinary Time',
                'liturgical_color' => 'white',
                'first_reading_reference' => 'Revelation 12:1-6',
                'first_reading_text' => 'God\'s temple in heaven was opened, and the ark of his covenant could be seen in the temple. A great sign appeared in the sky, a woman clothed with the sun, with the moon under her feet, and on her head a crown of twelve stars. She was with child and wailed aloud in pain as she labored to give birth.',
                'responsorial_psalm_reference' => 'Psalm 45:10, 11, 12, 16',
                'responsorial_psalm_text' => 'R. The queen stands at your right hand, arrayed in gold. The queen takes her place at your right hand in gold of Ophir. Listen, O daughter, and understand; pay me heed. Forget your people and your father\'s house.',
                'gospel_reference' => 'Luke 1:39-56',
                'gospel_text' => 'Mary set out and traveled to the hill country in haste to a town of Judah, where she entered the house of Zechariah and greeted Elizabeth. When Elizabeth heard Mary\'s greeting, the infant leaped in her womb, and Elizabeth, filled with the Holy Spirit, cried out in a loud voice and said, "Most blessed are you among women, and blessed is the fruit of your womb." Mary said: "My soul proclaims the greatness of the Lord; my spirit rejoices in God my savior."',
            ],
            "{$year}-11-01" => [
                'feast_name' => 'All Saints',
                'liturgical_season' => 'Ordinary Time',
                'liturgical_color' => 'white',
                'first_reading_reference' => 'Revelation 7:2-4, 9-14',
                'first_reading_text' => 'I, John, saw another angel come up from the East, holding the seal of the living God. He cried out in a loud voice to the four angels who were given power to damage the land and the sea, "Do not damage the land or the sea or the trees until we put the seal on the foreheads of the servants of our God." I heard the number of those who had been marked with the seal, one hundred and forty-four thousand marked from every tribe of the Israelites.',
                'responsorial_psalm_reference' => 'Psalm 24:1, 2, 3, 4, 5',
                'responsorial_psalm_text' => 'R. Lord, this is the people that longs to see your face. The LORD\'s are the earth and its fullness; the world and those who dwell in it. For he founded it upon the seas and established it upon the rivers.',
                'gospel_reference' => 'Matthew 5:1-12',
                'gospel_text' => 'When Jesus saw the crowds, he went up the mountain, and after he had sat down, his disciples came to him. He began to teach them, saying: "Blessed are the poor in spirit, for theirs is the kingdom of heaven. Blessed are they who mourn, for they will be comforted. Blessed are the meek, for they will inherit the land. Blessed are they who hunger and thirst for righteousness, for they will be satisfied. Blessed are the merciful, for they will be shown mercy. Blessed are the clean of heart, for they will see God. Blessed are the peacemakers, for they will be called children of God. Blessed are they who are persecuted for the sake of righteousness, for theirs is the kingdom of heaven."',
            ],
            "{$year}-12-08" => [
                'feast_name' => 'Immaculate Conception of the Blessed Virgin Mary',
                'liturgical_season' => 'Advent',
                'liturgical_color' => 'white',
                'first_reading_reference' => 'Genesis 3:9-15, 20',
                'first_reading_text' => 'The LORD God called to the man and asked him, "Where are you?" He answered, "I heard you in the garden; but I was afraid, because I was naked, so I hid myself." Then the LORD God said to the woman: "What is this you have done?" The woman answered, "The serpent tricked me into it, so I ate it." Then the LORD God said to the serpent: "Because you have done this, you shall be banned from all the animals and from all the wild creatures; on your belly shall you crawl, and dirt shall you eat all the days of your life. I will put enmity between you and the woman, and between your offspring and hers; he will strike at your head, while you strike at his heel."',
                'responsorial_psalm_reference' => 'Psalm 98:1, 2-3, 4',
                'responsorial_psalm_text' => 'R. Sing to the Lord a new song, for he has done marvelous deeds. Sing to the LORD a new song, for he has done wondrous deeds; his right hand has won victory for him, his holy arm.',
                'gospel_reference' => 'Luke 1:26-38',
                'gospel_text' => 'The angel Gabriel was sent from God to a town of Galilee called Nazareth, to a virgin betrothed to a man named Joseph, of the house of David, and the virgin\'s name was Mary. And coming to her, he said, "Hail, full of grace! The Lord is with you." But she was greatly troubled at what was said and pondered what sort of greeting this might be. Then the angel said to her, "Do not be afraid, Mary, for you have found favor with God. Behold, you will conceive in your womb and bear a son, and you shall name him Jesus." Mary said, "Behold, I am the handmaid of the Lord. May it be done to me according to your word."',
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
     * @return array<string, array<int, array<string, string|null>>>
     */
    protected function loadReadingsPool(): array
    {
        if ($this->readingsPool !== null) {
            return $this->readingsPool;
        }

        $path = database_path('seeders/data/mass_guides/readings_pool.json');

        if (! File::exists($path)) {
            return $this->readingsPool = ['sunday' => [], 'weekday' => []];
        }

        $payload = json_decode(File::get($path), true);

        return $this->readingsPool = [
            'sunday' => $payload['sunday'] ?? [],
            'weekday' => $payload['weekday'] ?? [],
        ];
    }

    /**
     * @return array<string, string|null>
     */
    protected function fallbackReadings(Carbon $date, bool $isSunday): array
    {
        $readings = [
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

        return $this->enrichFromBible($readings);
    }

    protected function resolveSeason(Carbon $date): string
    {
        $month = $date->month;
        $day = $date->day;

        if (($month === 12 && $day >= 25) || ($month === 1 && $day <= 12)) {
            return 'Christmas';
        }

        if ($month === 12 && $day < 25) {
            return 'Advent';
        }

        if ($month === 11 && $day >= 27) {
            return 'Advent';
        }

        if (($month === 2 && $day >= 14) || ($month === 3) || ($month === 4 && $day <= 17)) {
            return 'Lent';
        }

        if (($month === 4 && $day >= 18) || ($month === 5) || ($month === 6 && $day <= 10)) {
            return 'Easter';
        }

        return 'Ordinary Time';
    }

    protected function resolveFeastName(Carbon $date): string
    {
        $feasts = [
            '12-25' => 'Nativity of the Lord',
            '01-01' => 'Solemnity of Mary, Mother of God',
            '01-06' => 'Epiphany of the Lord',
            '03-19' => 'Solemnity of Saint Joseph',
            '08-15' => 'Assumption of the Blessed Virgin Mary',
            '11-01' => 'All Saints',
            '12-08' => 'Immaculate Conception',
        ];

        $key = $date->format('m-d');

        if (isset($feasts[$key])) {
            return $feasts[$key];
        }

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
