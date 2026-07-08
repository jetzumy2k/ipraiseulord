<?php

namespace App\Services;

use App\Models\MassGuide;
use Carbon\Carbon;

class MassOrderService
{
    public function __construct(
        protected BiblePassageResolver $biblePassageResolver,
    ) {}

    public function build(MassGuide $mass): array
    {
        $date = Carbon::parse($mass->liturgical_date);
        $isSunday = $date->isSunday();
        $season = $mass->liturgical_season ?? 'Ordinary Time';
        $isLent = $season === 'Lent';
        $includeGloria = $isSunday && ! $isLent;
        $includeCreed = $isSunday;
        $includeSecondReading = filled($mass->second_reading_text);
        $includeAlleluia = filled($mass->alleluia_text) && ! $isLent;
        $includeUniversalPrayer = $isSunday;

        return [
            'sections' => array_values(array_filter([
                $this->introductoryRites($includeGloria, $isSunday, $isLent),
                $this->liturgyOfTheWord($mass, $includeSecondReading, $includeAlleluia, $includeCreed, $includeUniversalPrayer),
                $this->liturgyOfTheEucharist($season, $isSunday),
                $this->concludingRites(),
            ])),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function introductoryRites(bool $includeGloria, bool $isSunday, bool $isLent): array
    {
        $steps = [
            $this->step('Entrance', [
                $this->part('priest', 'The priest and ministers enter in procession. The congregation sings an entrance hymn or antiphon.'),
            ]),
            $this->step('Sign of the Cross', [
                $this->part('priest', 'In the name of the Father, and of the Son, and of the Holy Spirit.'),
                $this->part('people', 'Amen.'),
            ]),
            $this->step('Greeting', [
                $this->part('priest', 'Grace to you and peace from God our Father and the Lord Jesus Christ.'),
                $this->part('people', 'And with your spirit.'),
            ]),
        ];

        if ($isSunday) {
            $steps[] = $this->step('Penitential Act (Form A)', [
                $this->part('priest', 'Brethren, let us acknowledge our sins, and so prepare ourselves to celebrate the sacred mysteries.'),
                $this->part('all', 'I confess to almighty God and to you, my brothers and sisters, that I have greatly sinned, in my thoughts and in my words, in what I have done and in what I have failed to do, through my fault, through my fault, through my most grievous fault; therefore I ask blessed Mary ever-Virgin, all the Angels and Saints, and you, my brothers and sisters, to pray for me to the Lord our God.'),
                $this->part('priest', 'May almighty God have mercy on us, forgive us our sins, and bring us to everlasting life.'),
                $this->part('all', 'Amen.'),
            ]);
        } else {
            $steps[] = $this->step('Penitential Act (Form C)', [
                $this->part('priest', 'You were sent to heal the contrite of heart.'),
                $this->part('people', 'Lord, have mercy.'),
                $this->part('priest', 'You came to call sinners.'),
                $this->part('people', 'Christ, have mercy.'),
                $this->part('priest', 'You are seated at the right hand of the Father to intercede for us.'),
                $this->part('people', 'Lord, have mercy.'),
            ]);
        }

        if ($includeGloria) {
            $steps[] = $this->step('Glory to God', [
                $this->part('all', 'Glory to God in the highest, and on earth peace to people of good will. We praise you, we bless you, we adore you, we glorify you, we give you thanks for your great glory, Lord God, heavenly King, O God, almighty Father. Lord Jesus Christ, Only Begotten Son, Lord God, Lamb of God, Son of the Father, you take away the sins of the world, have mercy on us; you take away the sins of the world, receive our prayer; you are seated at the right hand of the Father, have mercy on us. For you alone are the Holy One, you alone are the Lord, you alone are the Most High, Jesus Christ, with the Holy Spirit, in the glory of God the Father. Amen.'),
            ]);
        } elseif ($isLent && ! $isSunday) {
            $steps[] = $this->step('Note', [
                $this->part('note', 'During Lent, the Gloria is omitted at weekday Mass.'),
            ]);
        }

        $steps[] = $this->step('Collect', [
            $this->part('priest', 'Let us pray. [The priest says the Collect of the day in silence, then aloud.]'),
            $this->part('all', 'Amen.'),
        ]);

        return [
            'id' => 'introductory',
            'title' => 'Introductory Rites',
            'steps' => $steps,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function liturgyOfTheWord(
        MassGuide $mass,
        bool $includeSecondReading,
        bool $includeAlleluia,
        bool $includeCreed,
        bool $includeUniversalPrayer,
    ): array {
        $steps = [];

        if ($mass->first_reading_text) {
            $steps[] = $this->readingStep('First Reading', $mass->first_reading_reference, [
                $this->part('reader', 'A reading from the Book of '.($mass->first_reading_reference ? explode(' ', $mass->first_reading_reference)[0] : 'Scripture').'.'),
                $this->part('reading', $mass->first_reading_text),
                $this->part('all', 'The word of the Lord.'),
                $this->part('people', 'Thanks be to God.'),
            ]);
        }

        if ($mass->responsorial_psalm_text) {
            $steps[] = $this->readingStep('Responsorial Psalm', $mass->responsorial_psalm_reference, [
                $this->part('reading', $mass->responsorial_psalm_text),
            ]);
        }

        if ($includeSecondReading && $mass->second_reading_text) {
            $steps[] = $this->readingStep('Second Reading', $mass->second_reading_reference, [
                $this->part('reader', 'A reading from the Letter of '.($mass->second_reading_reference ? explode(' ', $mass->second_reading_reference)[0] : 'the Apostle').'.'),
                $this->part('reading', $mass->second_reading_text),
                $this->part('all', 'The word of the Lord.'),
                $this->part('people', 'Thanks be to God.'),
            ]);
        }

        if ($includeAlleluia && $mass->alleluia_text) {
            $steps[] = $this->readingStep('Gospel Acclamation', $mass->alleluia_reference, [
                $this->part('all', $mass->alleluia_text),
            ]);
        } elseif (! $includeAlleluia && $mass->gospel_reference) {
            $steps[] = $this->step('Verse Before the Gospel', [
                $this->part('all', 'Praise to you, Lord Jesus Christ, King of endless glory!'),
            ]);
        }

        if ($mass->gospel_text) {
            $steps[] = $this->readingStep('Gospel', $mass->gospel_reference, [
                $this->part('priest', 'The Lord be with you.'),
                $this->part('people', 'And with your spirit.'),
                $this->part('priest', 'A reading from the holy Gospel according to '.($mass->gospel_reference ? explode(' ', $mass->gospel_reference)[0] : 'Matthew').'.'),
                $this->part('people', 'Glory to you, O Lord.'),
                $this->part('reading', $mass->gospel_text),
                $this->part('priest', 'The Gospel of the Lord.'),
                $this->part('people', 'Praise to you, Lord Jesus Christ.'),
            ]);
        }

        $steps[] = $this->step('Homily', [
            $this->part('priest', 'The priest or deacon preaches the homily, explaining the readings and applying the Gospel to daily life.'),
        ]);

        if ($includeCreed) {
            $steps[] = $this->step('Profession of Faith (Nicene Creed)', [
                $this->part('all', 'I believe in one God, the Father almighty, maker of heaven and earth, of all things visible and invisible. I believe in one Lord Jesus Christ, the Only Begotten Son of God, born of the Father before all ages. God from God, Light from Light, true God from true God, begotten, not made, consubstantial with the Father; through him all things were made. For us men and for our salvation he came down from heaven, and by the Holy Spirit was incarnate of the Virgin Mary, and became man. For our sake he was crucified under Pontius Pilate, he suffered death and was buried, and rose again on the third day in accordance with the Scriptures. He ascended into heaven and is seated at the right hand of the Father. He will come again in glory to judge the living and the dead and his kingdom will have no end. I believe in the Holy Spirit, the Lord, the giver of life, who proceeds from the Father and the Son, who with the Father and the Son is adored and glorified, who has spoken through the prophets. I believe in one, holy, catholic and apostolic Church. I confess one Baptism for the forgiveness of sins and I look forward to the resurrection of the dead and the life of the world to come. Amen.'),
            ]);
        }

        if ($includeUniversalPrayer) {
            $steps[] = $this->step('Universal Prayer (Prayer of the Faithful)', [
                $this->part('priest', 'Brothers and sisters, let us ask the Lord for the needs of the Church and of the world.'),
                $this->part('reader', 'For the Church throughout the world, that she may be a sign of unity and peace, we pray to the Lord.'),
                $this->part('people', 'Lord, hear our prayer.'),
                $this->part('reader', 'For all who govern and serve in public office, that they may seek the common good, we pray to the Lord.'),
                $this->part('people', 'Lord, hear our prayer.'),
                $this->part('reader', 'For those who suffer in body, mind, or spirit, and for the faithful departed, we pray to the Lord.'),
                $this->part('people', 'Lord, hear our prayer.'),
                $this->part('priest', 'God our Father, hear the prayers we offer, and grant what we ask in faith, through Christ our Lord.'),
                $this->part('all', 'Amen.'),
            ]);
        }

        return [
            'id' => 'word',
            'title' => 'Liturgy of the Word',
            'steps' => $steps,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function liturgyOfTheEucharist(string $season, bool $isSunday): array
    {
        return [
            'id' => 'eucharist',
            'title' => 'Liturgy of the Eucharist',
            'steps' => [
                $this->step('Preparation of the Altar and Gifts', [
                    $this->part('priest', 'Blessed are you, Lord God of all creation, for through your goodness we have received the bread we offer you: fruit of the earth and work of human hands, it will become for us the bread of life.'),
                    $this->part('people', 'Blessed be God forever.'),
                    $this->part('priest', 'Blessed are you, Lord God of all creation, for through your goodness we have received the wine we offer you: fruit of the vine and work of human hands, it will become our spiritual drink.'),
                    $this->part('people', 'Blessed be God forever.'),
                    $this->part('priest', 'Pray, brethren, that my sacrifice and yours may be acceptable to God, the almighty Father.'),
                    $this->part('people', 'May the Lord accept the sacrifice at your hands for the praise and glory of his name, for our good and the good of all his holy Church.'),
                ]),
                $this->step('Prayer over the Offerings', [
                    $this->part('priest', 'The priest says the Prayer over the Offerings for the day.'),
                    $this->part('all', 'Amen.'),
                ]),
                $this->step('Eucharistic Prayer', [
                    $this->part('priest', 'The Lord be with you.'),
                    $this->part('people', 'And with your spirit.'),
                    $this->part('priest', 'Lift up your hearts.'),
                    $this->part('people', 'We lift them up to the Lord.'),
                    $this->part('priest', 'Let us give thanks to the Lord our God.'),
                    $this->part('people', 'It is right and just.'),
                    $this->part('priest', 'The priest continues with the Preface appropriate to the season ('.$season.') and the Eucharistic Prayer, giving thanks and calling down the Holy Spirit upon the gifts.'),
                ]),
                $this->step('Sanctus', [
                    $this->part('all', 'Holy, Holy, Holy Lord God of hosts. Heaven and earth are full of your glory. Hosanna in the highest. Blessed is he who comes in the name of the Lord. Hosanna in the highest.'),
                ]),
                $this->step('Memorial Acclamation', [
                    $this->part('priest', 'The mystery of faith.'),
                    $this->part('people', 'We proclaim your Death, O Lord, and profess your Resurrection until you come again.'),
                ]),
                $this->step('Great Amen', [
                    $this->part('priest', 'Through him, and with him, and in him, O God, almighty Father, in the unity of the Holy Spirit, all glory and honor is yours, for ever and ever.'),
                    $this->part('all', 'Amen.'),
                ]),
                $this->step('Lord\'s Prayer', [
                    $this->part('priest', 'At the Savior\'s command and formed by divine teaching, we dare to say:'),
                    $this->part('all', 'Our Father, who art in heaven, hallowed be thy name; thy kingdom come; thy will be done on earth as it is in heaven. Give us this day our daily bread; and forgive us our trespasses as we forgive those who trespass against us; and lead us not into temptation, but deliver us from evil.'),
                    $this->part('priest', 'Deliver us, Lord, we pray, from every evil, graciously grant peace in our days, that, by the help of your mercy, we may be always free from sin and safe from all distress, as we await the blessed hope and the coming of our Savior, Jesus Christ.'),
                    $this->part('all', 'For the kingdom, the power and the glory are yours now and for ever.'),
                ]),
                $this->step('Sign of Peace', [
                    $this->part('priest', 'Lord Jesus Christ, who said to your Apostles: Peace I leave you, my peace I give you, look not on our sins, but on the faith of your Church, and graciously grant her peace and unity in accordance with your will. Who live and reign for ever and ever.'),
                    $this->part('all', 'Amen.'),
                    $this->part('priest', 'The peace of the Lord be with you always.'),
                    $this->part('people', 'And with your spirit.'),
                    $this->part('priest', 'Let us offer each other the sign of peace.'),
                ]),
                $this->step('Lamb of God', [
                    $this->part('priest', 'Behold the Lamb of God, behold him who takes away the sins of the world. Blessed are those called to the supper of the Lamb.'),
                    $this->part('people', 'Lord, I am not worthy that you should enter under my roof, but only say the word and my soul shall be healed.'),
                ]),
                $this->step('Communion', [
                    $this->part('priest', 'The priest receives Communion and then distributes the Body and Blood of Christ to the faithful.'),
                    $this->part('note', $isSunday
                        ? 'During Communion, a suitable hymn or antiphon is sung.'
                        : 'During Communion, a psalm, hymn, or period of sacred silence is observed.'),
                ]),
                $this->step('Prayer after Communion', [
                    $this->part('priest', 'The priest says the Prayer after Communion for the day.'),
                    $this->part('all', 'Amen.'),
                ]),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function concludingRites(): array
    {
        return [
            'id' => 'concluding',
            'title' => 'Concluding Rites',
            'steps' => [
                $this->step('Blessing', [
                    $this->part('priest', 'The Lord be with you.'),
                    $this->part('people', 'And with your spirit.'),
                    $this->part('priest', 'May almighty God bless you, the Father, and the Son, and the Holy Spirit.'),
                    $this->part('people', 'Amen.'),
                ]),
                $this->step('Dismissal', [
                    $this->part('priest', 'Go forth, the Mass is ended.'),
                    $this->part('people', 'Thanks be to God.'),
                    $this->part('note', 'The priest and ministers leave in procession. The congregation may sing a closing hymn.'),
                ]),
            ],
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $parts
     * @return array<string, mixed>
     */
    protected function readingStep(string $title, ?string $reference, array $parts): array
    {
        $step = $this->step($title, $parts);

        if (filled($reference)) {
            $step['reference'] = $reference;
            $step['bible_url'] = $this->biblePassageResolver->buildUrl($reference);
        }

        return $step;
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
            'priest' => 'Priest',
            'people' => 'People',
            'all' => 'All',
            'reader' => 'Reader / Lector',
            'reading' => 'Reading',
            'note' => 'Note',
            default => ucfirst($role),
        };
    }
}
