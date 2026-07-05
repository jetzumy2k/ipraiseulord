<?php

namespace Database\Seeders;

use App\Models\DailyPsalm;
use Illuminate\Database\Seeder;

class DailyPsalmSeeder extends Seeder
{
    public function run(): void
    {
        $psalms = [
            ['psalm_number' => 1, 'title' => 'The Two Ways', 'reference' => 'Psalm 1', 'text' => 'Blessed is the man who walks not in the counsel of the wicked, nor stands in the way of sinners, nor sits in the seat of scoffers; but his delight is in the law of the LORD, and on his law he meditates day and night.'],
            ['psalm_number' => 8, 'title' => 'The Glory of God', 'reference' => 'Psalm 8', 'text' => 'O LORD, our Lord, how majestic is thy name in all the earth! Thou whose glory above the heavens is chanted by the mouth of babes and infants.'],
            ['psalm_number' => 19, 'title' => 'God\'s Glory in Creation', 'reference' => 'Psalm 19', 'text' => 'The heavens are telling the glory of God; and the firmament proclaims his handiwork. Day to day pours forth speech, and night to night declares knowledge.'],
            ['psalm_number' => 23, 'title' => 'The Good Shepherd', 'reference' => 'Psalm 23', 'text' => 'The LORD is my shepherd; I shall not want. He makes me lie down in green pastures; he leads me beside still waters; he restores my soul.'],
            ['psalm_number' => 27, 'title' => 'Trust in God', 'reference' => 'Psalm 27', 'text' => 'The LORD is my light and my salvation; whom shall I fear? The LORD is the stronghold of my life; of whom shall I be afraid?'],
            ['psalm_number' => 34, 'title' => 'Praise for Deliverance', 'reference' => 'Psalm 34', 'text' => 'I will bless the LORD at all times; his praise shall continually be in my mouth. My soul makes its boast in the LORD; let the afflicted hear and be glad.'],
            ['psalm_number' => 46, 'title' => 'God Our Refuge', 'reference' => 'Psalm 46', 'text' => 'God is our refuge and strength, a very present help in trouble. Therefore we will not fear though the earth should change, though the mountains shake in the heart of the sea.'],
            ['psalm_number' => 51, 'title' => 'Prayer for Mercy', 'reference' => 'Psalm 51', 'text' => 'Have mercy on me, O God, according to thy steadfast love; according to thy abundant mercy blot out my transgressions. Wash me thoroughly from my iniquity, and cleanse me from my sin.'],
            ['psalm_number' => 63, 'title' => 'Longing for God', 'reference' => 'Psalm 63', 'text' => 'O God, thou art my God, I seek thee, my soul thirsts for thee; my flesh faints for thee, as in a dry and weary land where no water is.'],
            ['psalm_number' => 84, 'title' => 'Longing for God\'s House', 'reference' => 'Psalm 84', 'text' => 'How lovely is thy dwelling place, O LORD of hosts! My soul longs, yea, faints for the courts of the LORD; my heart and flesh sing for joy to the living God.'],
            ['psalm_number' => 90, 'title' => 'God\'s Eternity', 'reference' => 'Psalm 90', 'text' => 'Lord, thou hast been our dwelling place in all generations. Before the mountains were brought forth, or ever thou hadst formed the earth and the world, from everlasting to everlasting thou art God.'],
            ['psalm_number' => 91, 'title' => 'Assurance of God\'s Protection', 'reference' => 'Psalm 91', 'text' => 'He who dwells in the shelter of the Most High, who abides in the shadow of the Almighty, will say to the LORD, "My refuge and my fortress; my God, in whom I trust."'],
            ['psalm_number' => 95, 'title' => 'A Call to Worship', 'reference' => 'Psalm 95', 'text' => 'O come, let us sing to the LORD; let us make a joyful noise to the rock of our salvation! Let us come into his presence with thanksgiving; let us make a joyful noise to him with songs of praise.'],
            ['psalm_number' => 100, 'title' => 'A Psalm for Giving Thanks', 'reference' => 'Psalm 100', 'text' => 'Make a joyful noise to the LORD, all the lands! Serve the LORD with gladness! Come into his presence with singing! Know that the LORD is God!'],
            ['psalm_number' => 103, 'title' => 'Thanksgiving for God\'s Mercy', 'reference' => 'Psalm 103', 'text' => 'Bless the LORD, O my soul; and all that is within me, bless his holy name! Bless the LORD, O my soul, and forget not all his benefits.'],
            ['psalm_number' => 116, 'title' => 'Thanksgiving for Deliverance', 'reference' => 'Psalm 116', 'text' => 'I love the LORD, because he has heard my voice and my supplications. Because he inclined his ear to me, therefore I will call on him as long as I live.'],
            ['psalm_number' => 121, 'title' => 'A Prayer for Protection', 'reference' => 'Psalm 121', 'text' => 'I lift up my eyes to the hills. From whence does my help come? My help comes from the LORD, who made heaven and earth.'],
            ['psalm_number' => 130, 'title' => 'Waiting for Divine Redemption', 'reference' => 'Psalm 130', 'text' => 'Out of the depths I cry to thee, O LORD! Lord, hear my voice! Let thy ears be attentive to the voice of my supplications.'],
            ['psalm_number' => 139, 'title' => 'The All-Knowing and Ever-Present God', 'reference' => 'Psalm 139', 'text' => 'O LORD, thou hast searched me and known me! Thou knowest when I sit down and when I rise up; thou discernest my thoughts from afar.'],
            ['psalm_number' => 150, 'title' => 'Praise the Lord!', 'reference' => 'Psalm 150', 'text' => 'Praise the LORD! Praise God in his sanctuary; praise him in his mighty firmament! Praise him for his mighty deeds; praise him according to his exceeding greatness!'],
        ];

        foreach ($psalms as $psalm) {
            DailyPsalm::updateOrCreate(
                ['psalm_number' => $psalm['psalm_number']],
                array_merge($psalm, ['is_active' => true])
            );
        }
    }
}
