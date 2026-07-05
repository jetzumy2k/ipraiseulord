<?php

namespace Database\Seeders;

use App\Models\Proverb;
use Illuminate\Database\Seeder;

class ProverbSeeder extends Seeder
{
    public function run(): void
    {
        $proverbs = [
            ['text' => 'Trust in the LORD with all your heart, and do not rely on your own insight.', 'reference' => 'Proverbs 3:5'],
            ['text' => 'The fear of the LORD is the beginning of knowledge; fools despise wisdom and instruction.', 'reference' => 'Proverbs 1:7'],
            ['text' => 'Commit your work to the LORD, and your plans will be established.', 'reference' => 'Proverbs 16:3'],
            ['text' => 'A soft answer turns away wrath, but a harsh word stirs up anger.', 'reference' => 'Proverbs 15:1'],
            ['text' => 'Pride goes before destruction, and a haughty spirit before a fall.', 'reference' => 'Proverbs 16:18'],
            ['text' => 'Train up a child in the way he should go, and when he is old he will not depart from it.', 'reference' => 'Proverbs 22:6'],
            ['text' => 'The righteous falls seven times, and rises again; but the wicked are overthrown by calamity.', 'reference' => 'Proverbs 24:16'],
            ['text' => 'Iron sharpens iron, and one man sharpens another.', 'reference' => 'Proverbs 27:17'],
            ['text' => 'Where there is no vision, the people perish.', 'reference' => 'Proverbs 29:18'],
            ['text' => 'A friend loves at all times, and a brother is born for adversity.', 'reference' => 'Proverbs 17:17'],
            ['text' => 'Better is a little with righteousness than great revenues with injustice.', 'reference' => 'Proverbs 16:8'],
            ['text' => 'The heart of the righteous ponders how to answer, but the mouth of the wicked pours out evil things.', 'reference' => 'Proverbs 15:28'],
            ['text' => 'He who is slow to anger is better than the mighty, and he who rules his spirit than he who takes a city.', 'reference' => 'Proverbs 16:32'],
            ['text' => 'A good name is to be chosen rather than great riches, and favor is better than silver or gold.', 'reference' => 'Proverbs 22:1'],
            ['text' => 'The LORD gives wisdom; from his mouth come knowledge and understanding.', 'reference' => 'Proverbs 2:6'],
            ['text' => 'Do not withhold good from those to whom it is due, when it is in your power to do it.', 'reference' => 'Proverbs 3:27'],
            ['text' => 'Keep your heart with all vigilance; for from it flow the springs of life.', 'reference' => 'Proverbs 4:23'],
            ['text' => 'The tongue of the righteous is choice silver; the mind of the wicked is of little worth.', 'reference' => 'Proverbs 10:20'],
            ['text' => 'Whoever walks in integrity walks securely, but he who perverts his ways will be found out.', 'reference' => 'Proverbs 10:9'],
            ['text' => 'Hatred stirs up strife, but love covers all offenses.', 'reference' => 'Proverbs 10:12'],
            ['text' => 'The blessing of the LORD makes rich, and he adds no sorrow with it.', 'reference' => 'Proverbs 10:22'],
            ['text' => 'When pride comes, then comes disgrace; but with humility comes wisdom.', 'reference' => 'Proverbs 11:2'],
            ['text' => 'Whoever gives thought to the word will discover good, and blessed is he who trusts in the LORD.', 'reference' => 'Proverbs 16:20'],
            ['text' => 'Gray hair is a crown of glory; it is gained in a righteous life.', 'reference' => 'Proverbs 16:31'],
            ['text' => 'Even a fool who keeps silent is considered wise; when he closes his lips, he is deemed intelligent.', 'reference' => 'Proverbs 17:28'],
            ['text' => 'The name of the LORD is a strong tower; the righteous man runs into it and is safe.', 'reference' => 'Proverbs 18:10'],
            ['text' => 'Many are the plans in the mind of a man, but it is the purpose of the LORD that will be established.', 'reference' => 'Proverbs 19:21'],
            ['text' => 'Wine is a mocker, strong drink a brawler; and whoever is led astray by it is not wise.', 'reference' => 'Proverbs 20:1'],
            ['text' => 'The crucible is for silver, and the furnace is for gold, and the LORD tries hearts.', 'reference' => 'Proverbs 17:3'],
            ['text' => 'Charm is deceitful, and beauty is vain, but a woman who fears the LORD is to be praised.', 'reference' => 'Proverbs 31:30'],
        ];

        foreach ($proverbs as $proverb) {
            Proverb::updateOrCreate(
                ['reference' => $proverb['reference']],
                [
                    'text' => $proverb['text'],
                    'bible_version' => 'RSVCE',
                    'is_active' => true,
                ]
            );
        }
    }
}
